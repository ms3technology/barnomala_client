<?php

namespace App\Services\DataTransfer;

use App\Models\Gallery;
use App\Models\News;
use App\Models\NewsArtifact;
use App\Models\Notice;
use App\Models\NoticeArtifact;
use App\Models\Option;
use App\Models\Speech;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use RuntimeException;

class WordPressTransferService
{
    public function previewLegacyOrders(int $limit = 20): Collection
    {
        return DB::connection('wordpress')
            ->table('orders')
            ->orderByDesc('id')
            ->limit(max(1, min($limit, 200)))
            ->get();
    }

    public function getSpeechTransferPreview(): array
    {
        $source = $this->getWordPressSpeechSource();
        $payloads = $this->buildSpeechPayloads($source);

        $candidates = [];
        foreach ($payloads as $payload) {
            $current = Speech::query()
                ->where('row_index', $payload['row_index'])
                ->where('column_index', $payload['column_index'])
                ->first();

            $isUpToDate = $current
                && trim((string) $current->title) === trim((string) $payload['title'])
                && trim((string) $current->name) === trim((string) $payload['name'])
                && trim((string) ($current->speech ?? '')) === trim((string) ($payload['speech'] ?? ''));

            $candidates[] = [
                'key' => $payload['key'],
                'row_index' => $payload['row_index'],
                'column_index' => $payload['column_index'],
                'title' => $payload['title'],
                'name' => $payload['name'],
                'has_source' => $payload['has_source'],
                'exists' => (bool) $current,
                'up_to_date' => $isUpToDate,
            ];
        }

        return [
            'required_source_keys' => array_keys($this->getSpeechSourceMapping()),
            'source_values' => $source,
            'source_ready_count' => count(array_filter($source, fn ($value) => ! blank($value))),
            'primary_speeches_count' => Speech::query()->count(),
            'candidates' => $candidates,
            'can_transfer' => count(array_filter($payloads, fn ($payload) => $payload['has_source'])) > 0,
        ];
    }

    public function transferSpeechesFromWordPress(): array
    {
        $source = $this->getWordPressSpeechSource();
        $payloads = $this->buildSpeechPayloads($source);

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $optionsUpdated = 0;

        DB::transaction(function () use ($source, $payloads, &$created, &$updated, &$skipped, &$optionsUpdated): void {
            foreach ($this->getOptionMapForSpeechTransfer() as $wpKey => $targetKey) {
                $value = $source[$wpKey] ?? null;
                if (blank($value)) {
                    continue;
                }

                Option::set($targetKey, $value);
                $optionsUpdated++;
            }

            foreach ($payloads as $payload) {
                if (! $payload['has_source']) {
                    $skipped++;
                    continue;
                }

                $existingSpeech = Speech::query()
                    ->where('row_index', $payload['row_index'])
                    ->where('column_index', $payload['column_index'])
                    ->first();

                $imageData = null;
                if (!empty($payload['image_url'])) {
                    $imageData = $this->downloadAndProcessImage($payload['image_url'], 'speeches');
                }

                if ($imageData === null && $existingSpeech) {
                    $imageData = $existingSpeech->image_json;
                }

                $speech = Speech::updateOrCreate(
                    [
                        'row_index' => $payload['row_index'],
                        'column_index' => $payload['column_index'],
                    ],
                    [
                        'name' => $payload['name'],
                        'title' => $payload['title'],
                        'speech' => $payload['speech'],
                        'image_json' => $imageData,
                        'colspan' => 1,
                        'is_active' => true,
                    ]
                );

                if ($speech->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            }
        });

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'options_updated' => $optionsUpdated,
        ];
    }

    public function transferSliderImagesFromWordPress(): array
    {
        if (! Schema::connection('wordpress')->hasTable('sm_slider_images')) {
            throw new RuntimeException('sm_slider_images table not found in wordpress connection.');
        }

        $sourceImages = DB::connection('wordpress')
            ->table('sm_slider_images')
            ->get();

        $sliderJson = [];
        $transferred = 0;

        foreach ($sourceImages as $img) {
            $imageData = $this->downloadAndProcessImage($img->image_url, 'sliders');
            if ($imageData) {
                $sliderJson[] = $imageData;
                $transferred++;
            }
        }

        if (! empty($sliderJson)) {
            Option::set('institute.branding.slider_json', $sliderJson);
        }

        return [
            'transferred' => $transferred,
            'total_source' => $sourceImages->count(),
            'option_updated' => ! empty($sliderJson),
        ];
    }

    public function transferGalleriesFromWordPress(): array
    {
        $sourceRows = DB::connection('wordpress')->select(
            <<<'SQL'
            SELECT p.ID, p.post_date, p.post_content, p.post_name
            FROM sm_posts p
            JOIN sm_term_relationships tr ON p.ID = tr.object_id
            JOIN sm_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            JOIN sm_terms t ON tt.term_id = t.term_id
            WHERE p.post_status = 'publish'
              AND tt.taxonomy = 'category'
              AND t.slug = 'gallery'
            ORDER BY p.ID ASC
            SQL
        );

        $created = 0;
        $updated = 0;
        $skippedNoImage = 0;
        $failedImageDownload = 0;

        DB::transaction(function () use (
            $sourceRows,
            &$created,
            &$updated,
            &$skippedNoImage,
            &$failedImageDownload
        ): void {
            foreach ($sourceRows as $row) {
                $legacyId = (int) ($row->ID ?? 0);
                if ($legacyId <= 0) {
                    continue;
                }

                $existing = Gallery::query()->where('legacy_id', $legacyId)->first();
                $imagePath = $existing?->image_path;

                $imageUrl = $this->extractImageUrlFromHtml((string) ($row->post_content ?? ''));
                if ($imageUrl !== null) {
                    $imageData = $this->downloadAndProcessImage($imageUrl, 'gallery/photos');
                    if ($imageData !== null) {
                        $imagePath = $imageData['path'] ?? $imagePath;
                    } else {
                        $failedImageDownload++;
                    }
                } elseif (! $existing) {
                    $skippedNoImage++;
                    continue;
                }

                $gallery = Gallery::updateOrCreate(
                    ['legacy_id' => $legacyId],
                    [
                        'type' => Gallery::TYPE_PHOTO,
                        'title' => trim((string) ($row->post_name ?? '')),
                        'category' => 'imported',
                        'date' => $row->post_date,
                        'image_path' => $imagePath,
                    ]
                );

                if ($gallery->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            }
        });

        return [
            'created' => $created,
            'updated' => $updated,
            'total_source' => count($sourceRows),
            'skipped_no_image' => $skippedNoImage,
            'failed_image_download' => $failedImageDownload,
        ];
    }

    public function transferNoticesFromWordPress(): array
    {
        $sourceRows = DB::connection('wordpress')->select(
            <<<'SQL'
            SELECT p.ID, p.post_date, p.post_content, p.post_title
            FROM sm_posts p
            JOIN sm_term_relationships tr ON p.ID = tr.object_id
            JOIN sm_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            JOIN sm_terms t ON tt.term_id = t.term_id
            WHERE p.post_status = 'publish'
              AND tt.taxonomy = 'category'
              AND t.slug = 'latest-notice'
            ORDER BY p.ID ASC
            SQL
        );

        $created = 0;
        $updated = 0;
        $artifactDownloaded = 0;
        $artifactFailed = 0;

        DB::transaction(function () use ($sourceRows, &$created, &$updated, &$artifactDownloaded, &$artifactFailed): void {
            foreach ($sourceRows as $row) {
                $legacyId = (int) ($row->ID ?? 0);
                if ($legacyId <= 0) {
                    continue;
                }

                $parsed = $this->extractContentAndMediaUrls((string) ($row->post_content ?? ''));

                $notice = Notice::query()->updateOrCreate(
                    ['legacy_id' => $legacyId],
                    [
                        'title' => trim((string) ($row->post_title ?? '')),
                        'content' => $parsed['text'] !== '' ? $parsed['text'] : null,
                        'published_at' => $row->post_date,
                        'is_active' => true,
                    ]
                );

                if ($notice->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }

                $this->deleteNoticeArtifacts($notice);
                foreach ($parsed['urls'] as $url) {
                    $artifactData = $this->downloadAndStoreArtifact($url, 'notice-artifacts');
                    if ($artifactData === null) {
                        $artifactFailed++;
                        continue;
                    }

                    NoticeArtifact::query()->create([
                        'notice_id' => $notice->id,
                        'file_path' => $artifactData['file_path'],
                        'file_name' => $artifactData['file_name'],
                        'file_type' => $artifactData['file_type'],
                        'file_size' => $artifactData['file_size'],
                    ]);
                    $artifactDownloaded++;
                }
            }
        });

        return [
            'created' => $created,
            'updated' => $updated,
            'total_source' => count($sourceRows),
            'artifact_downloaded' => $artifactDownloaded,
            'artifact_failed' => $artifactFailed,
        ];
    }

    public function transferNewsFromWordPress(): array
    {
        $sourceRows = DB::connection('wordpress')->select(
            <<<'SQL'
            SELECT p.ID, p.post_date, p.post_content, p.post_title
            FROM sm_posts p
            JOIN sm_term_relationships tr ON p.ID = tr.object_id
            JOIN sm_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            JOIN sm_terms t ON tt.term_id = t.term_id
            WHERE p.post_status = 'publish'
              AND tt.taxonomy = 'category'
              AND t.slug = 'latest-news'
            ORDER BY p.ID ASC
            SQL
        );

        $created = 0;
        $updated = 0;
        $artifactDownloaded = 0;
        $artifactFailed = 0;

        DB::transaction(function () use ($sourceRows, &$created, &$updated, &$artifactDownloaded, &$artifactFailed): void {
            foreach ($sourceRows as $row) {
                $legacyId = (int) ($row->ID ?? 0);
                if ($legacyId <= 0) {
                    continue;
                }

                $parsed = $this->extractContentAndMediaUrls((string) ($row->post_content ?? ''));

                $news = News::query()->updateOrCreate(
                    ['legacy_id' => $legacyId],
                    [
                        'title' => trim((string) ($row->post_title ?? '')),
                        'summary' => Str::limit($parsed['text'], 220, ''),
                        'content' => $parsed['text'] !== '' ? $parsed['text'] : null,
                        'published_at' => $row->post_date,
                        'is_active' => true,
                    ]
                );

                if ($news->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }

                $this->deleteNewsArtifacts($news);
                $firstImage = null;

                foreach ($parsed['urls'] as $url) {
                    $artifactData = $this->downloadAndStoreArtifact($url, 'news-artifacts');
                    if ($artifactData === null) {
                        $artifactFailed++;
                        continue;
                    }

                    NewsArtifact::query()->create([
                        'news_id' => $news->id,
                        'file_path' => $artifactData['file_path'],
                        'file_name' => $artifactData['file_name'],
                        'file_type' => $artifactData['file_type'],
                        'file_size' => $artifactData['file_size'],
                    ]);

                    if ($firstImage === null && $this->isImageMimeType($artifactData['file_type'])) {
                        $firstImage = [
                            'path' => $artifactData['file_path'],
                            'url' => asset('storage/' . ltrim($artifactData['file_path'], '/')),
                            'provider' => 'local',
                        ];
                    }

                    $artifactDownloaded++;
                }

                if ($firstImage !== null) {
                    $news->image_json = $firstImage;
                    $news->save();
                }
            }
        });

        return [
            'created' => $created,
            'updated' => $updated,
            'total_source' => count($sourceRows),
            'artifact_downloaded' => $artifactDownloaded,
            'artifact_failed' => $artifactFailed,
        ];
    }

    private function getSpeechSourceMapping(): array
    {
        return [
            'aboutTitelText',
            'aboutUsText',
            'aboutUsMoreBtn',
            'footerAddress',
            'headmasterSpeechTitle',
            'homeHeadmasterTitle',
            'homeHeadmaster',
            'homeHeadmasterImg',
            'chairmanSpeechTitle',
            'homeChairmanTitle',
            'homeChairman',
            'homeChairmanImg',
        ];
    }

    private function getOptionMapForSpeechTransfer(): array
    {
        return [
            'aboutTitelText' => 'institute.about.title',
            'aboutUsText' => 'institute.about.text',
            'aboutUsMoreBtn' => 'institute.about.button_text',
            'footerAddress' => 'institute.footer.text',
        ];
    }

    private function getWordPressSpeechSource(): array
    {
        $sourceKeys = $this->getSpeechSourceMapping();

        if (! Schema::connection('wordpress')->hasTable('sm_options')) {
            throw new RuntimeException('sm_options table not found in wordpress connection.');
        }

        return DB::connection('wordpress')
            ->table('sm_options')
            ->whereIn('option_name', $sourceKeys)
            ->pluck('option_value', 'option_name')
            ->toArray();
    }

    private function buildSpeechPayloads(array $source): array
    {
        $headmasterHasSource = ! blank($source['homeHeadmaster'] ?? null);
        $chairmanHasSource = ! blank($source['homeChairman'] ?? null);

        return [
            [
                'key' => 'headmaster',
                'name' => ($source['homeHeadmasterTitle'] ?? '') ?: 'Headmaster',
                'title' => ($source['headmasterSpeechTitle'] ?? '') ?: 'Headmaster Speech',
                'speech' => ($source['homeHeadmaster'] ?? '') ?: '',
                'image_url' => $source['homeHeadmasterImg'] ?? null,
                'row_index' => 1,
                'column_index' => 1,
                'has_source' => $headmasterHasSource,
            ],
            [
                'key' => 'chairman',
                'name' => ($source['homeChairmanTitle'] ?? '') ?: 'Chairman',
                'title' => ($source['chairmanSpeechTitle'] ?? '') ?: 'Chairman Speech',
                'speech' => ($source['homeChairman'] ?? '') ?: '',
                'image_url' => $source['homeChairmanImg'] ?? null,
                'row_index' => 1,
                'column_index' => 2,
                'has_source' => $chairmanHasSource,
            ],
        ];
    }

    private function extractImageUrlFromHtml(string $html): ?string
    {
        $html = trim(html_entity_decode($html));
        if ($html === '') {
            return null;
        }

        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $matches) !== 1) {
            return null;
        }

        $url = trim((string) ($matches[1] ?? ''));

        return $url !== '' ? $url : null;
    }

    private function extractContentAndMediaUrls(string $html): array
    {
        $html = trim(html_entity_decode($html));
        if ($html === '') {
            return [
                'text' => '',
                'urls' => [],
            ];
        }

        $urls = [];

        if (preg_match_all('/<a[^>]+href=["\']([^"\']+)["\']/i', $html, $anchorMatches) > 0) {
            foreach ($anchorMatches[1] ?? [] as $url) {
                $normalized = $this->normalizeMediaUrl((string) $url);
                if ($normalized !== null) {
                    $urls[] = $normalized;
                }
            }
        }

        if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $imageMatches) > 0) {
            foreach ($imageMatches[1] ?? [] as $url) {
                $normalized = $this->normalizeMediaUrl((string) $url);
                if ($normalized !== null) {
                    $urls[] = $normalized;
                }
            }
        }

        $contentWithoutImages = preg_replace('/<img[^>]*>/i', ' ', $html) ?? $html;
        $contentWithoutLinks = preg_replace('/<a\b[^>]*>(.*?)<\/a>/is', '$1', $contentWithoutImages) ?? $contentWithoutImages;
        $plainText = trim(preg_replace('/\s+/u', ' ', strip_tags($contentWithoutLinks)) ?? '');

        return [
            'text' => $plainText,
            'urls' => array_values(array_unique($urls)),
        ];
    }

    private function normalizeMediaUrl(string $url): ?string
    {
        $url = trim(html_entity_decode($url));
        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        return $url;
    }

    private function downloadAndStoreArtifact(string $url, string $directory): ?array
    {
        try {
            $response = Http::timeout(30)
                ->retry(2, 300)
                ->get($url);

            if (! $response->successful()) {
                Log::warning('Failed to download artifact.', ['url' => $url]);
                return null;
            }

            $body = $response->body();
            if ($body === '') {
                Log::warning('Downloaded artifact is empty.', ['url' => $url]);
                return null;
            }

            $originalName = basename(parse_url($url, PHP_URL_PATH) ?: 'file');
            $originalName = trim($originalName) !== '' ? trim($originalName) : 'file';
            $safeName = preg_replace('/[^A-Za-z0-9._-]/', '-', $originalName) ?: 'file';
            $storedName = pathinfo($safeName, PATHINFO_FILENAME) . '-' . substr(md5($url . microtime()), 0, 8);
            $extension = pathinfo($safeName, PATHINFO_EXTENSION);
            if ($extension !== '') {
                $storedName .= '.' . $extension;
            }

            $path = $directory . '/' . $storedName;
            Storage::disk('public')->put($path, $body);

            $contentType = (string) $response->header('Content-Type', '');
            $contentType = trim(strtolower(explode(';', $contentType)[0]));
            $mimeType = $contentType !== '' ? $contentType : null;

            return [
                'file_path' => $path,
                'file_name' => $safeName,
                'file_type' => $mimeType,
                'file_size' => strlen($body),
            ];
        } catch (\Throwable $e) {
            Log::warning('Failed to download/store artifact.', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function deleteNoticeArtifacts(Notice $notice): void
    {
        $artifacts = $notice->artifacts()->get();
        foreach ($artifacts as $artifact) {
            if (! empty($artifact->file_path)) {
                Storage::disk('public')->delete($artifact->file_path);
            }
        }
        $notice->artifacts()->delete();
    }

    private function deleteNewsArtifacts(News $news): void
    {
        $artifacts = $news->artifacts()->get();
        foreach ($artifacts as $artifact) {
            if (! empty($artifact->file_path)) {
                Storage::disk('public')->delete($artifact->file_path);
            }
        }
        $news->artifacts()->delete();
    }

    private function isImageMimeType(?string $mimeType): bool
    {
        return is_string($mimeType) && str_starts_with($mimeType, 'image/');
    }

    private function downloadAndProcessImage(string $url, string $directory): ?array
    {
        try {
            $url = trim(html_entity_decode($url));
            if ($url === '') {
                return null;
            }

            $response = Http::timeout(30)
                ->retry(2, 300)
                ->get($url);

            if (!$response->successful()) {
                Log::error("Failed to download image from URL: {$url}");
                return null;
            }

            $extension = 'jpg';
            $filename = md5($url . time()) . '.' . $extension;
            $path = "{$directory}/{$filename}";

            $manager = new ImageManager(new Driver());
            $image = $manager->read($response->body());
            $encoded = $image->toJpeg(85);

            Storage::disk('public')->put($path, (string) $encoded);

            return [
                'path' => $path,
                'url' => asset('storage/' . ltrim($path, '/')),
                'provider' => 'local',
            ];
        } catch (\Exception $e) {
            Log::error("Failed to process speech image from URL: {$url}", [
                'exception' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
