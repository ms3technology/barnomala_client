<?php

namespace App\Services\DataTransfer;

use App\Models\Option;
use App\Models\Speech;
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
