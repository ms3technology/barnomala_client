<?php

namespace App\Services;

use App\Models\Gallery;
use App\Models\News;
use App\Models\NewsArtifact;
use App\Models\Notice;
use App\Models\NoticeArtifact;
use App\Models\Option;
use App\Models\Speech;
use App\Registries\OptionRegistry;
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
    public function seedWebsiteDefaults(): array
    {
        $optionsCreated = 0;
        $optionsUpdated = 0;
        $noticesCreated = 0;
        $noticesUpdated = 0;
        $galleriesCreated = 0;
        $galleriesUpdated = 0;
        $speechesCreated = 0;
        $speechesUpdated = 0;

        DB::transaction(function () use (
            &$optionsCreated,
            &$optionsUpdated,
            &$noticesCreated,
            &$noticesUpdated,
            &$galleriesCreated,
            &$galleriesUpdated,
            &$speechesCreated,
            &$speechesUpdated
        ): void {
            foreach (OptionRegistry::getRegistration() as $category) {
                foreach (($category['options'] ?? []) as $key => $meta) {
                    $existing = Option::query()->where('option_key', $key)->exists();
                    Option::set($key, $this->getSampleOptionValue($key, (string) ($meta['type'] ?? 'text')));

                    if ($existing) {
                        $optionsUpdated++;
                    } else {
                        $optionsCreated++;
                    }
                }
            }

            $sliderOptionExists = Option::query()->where('option_key', 'institute.branding.slider_json')->exists();
            Option::set('institute.branding.slider_json', [
                [
                    'url' => asset('images/default-slider.jpeg'),
                    'path' => 'images/default-slider.jpeg',
                    'title' => 'A Better Future Through Education',
                    'order' => 1,
                ],
            ]);

            if ($sliderOptionExists) {
                $optionsUpdated++;
            } else {
                $optionsCreated++;
            }

            $noticeSamples = [
                [
                    'title' => 'নতুন শিক্ষাবর্ষের ইউনিফর্ম ও পাঠ্যপুস্তক বিতরণ',
                    'content' => 'নতুন শিক্ষাবর্ষের সকল শিক্ষার্থীদের মধ্যে বিনামূল্যে পাঠ্যপুস্তক বিতরণ করা হবে। ১লা জানুয়ারি বই উৎসব পালন করা হবে।',
                    'published_at' => now()->subDay()->toDateString(),
                    'is_active' => true,
                    'is_urgent' => false,
                ],
            ];

            foreach ($noticeSamples as $sample) {
                $model = Notice::query()->updateOrCreate(['title' => $sample['title']], $sample);
                if ($model->wasRecentlyCreated) {
                    $noticesCreated++;
                } else {
                    $noticesUpdated++;
                }
            }

            $gallerySamples = [
                [
                    'legacy_id' => 900001,
                    'type' => Gallery::TYPE_PHOTO,
                    'title' => 'Sample Gallery 1',
                    'category' => 'sample',
                    'date' => now()->subDays(7)->toDateString(),
                    'image_path' => 'images/picture-1.jpg',
                    'description' => 'Sample gallery photo 1 for initial setup.',
                ],
                [
                    'legacy_id' => 900002,
                    'type' => Gallery::TYPE_PHOTO,
                    'title' => 'Sample Gallery 2',
                    'category' => 'sample',
                    'date' => now()->subDays(6)->toDateString(),
                    'image_path' => 'images/picture-2.jpg',
                    'description' => 'Sample gallery photo 2 for initial setup.',
                ],
            ];

            foreach ($gallerySamples as $sample) {
                $model = Gallery::query()->updateOrCreate(
                    ['legacy_id' => $sample['legacy_id']],
                    $sample
                );

                if ($model->wasRecentlyCreated) {
                    $galleriesCreated++;
                } else {
                    $galleriesUpdated++;
                }
            }

            $speechSamples = [
                [
                    'name' => 'Headmaster',
                    'title' => 'প্রধান শিক্ষকের বাণী',
                    'designation' => 'প্রধান শিক্ষক',
                    'speech' => 'সরকারি মডেল হাইস্কুল এন্ড কলেজ শিক্ষার অগ্রগতিতে অনন্য ভূমিকা রাখছে। শিক্ষার পাশাপাশি অত্র প্রতিষ্ঠানের শিক্ষার্থীরা সাহিত্য, সংস্কৃতি, খেলাধুলা, প্রযুক্তি, বিতর্ক, বিজ্ঞান সকল ক্ষেত্রে কৃতিত্ব অর্জন করে বিদ্যালয়ের সুনাম অক্ষুন্ন রেখে চলেছে। অত্র বিদ্যালয় থেকে একজন শিক্ষার্থী মেধাবী, সৎ, কর্মঠ, আধুনিক বিজ্ঞান মনষ্ক, যোগ্য দেশপ্রেমিক ও মানবিক মূল্যবোধ সম্পন্ন আলোকিত মানুষ হয়ে বাংলাদেশকে সমৃদ্ধের পথে এগিয়ে নিয়ে যাবে এ আমার দৃঢ় প্রত্যাশা। বিদ্যালয় দৃঢ়ভাবে বিশ্বাস করে সন্তানের শিক্ষার জন্য অভিভাবকের পরিকল্পিত বিনিয়োগই সর্বশ্রেষ্ঠ বিনিয়োগ। সন্তানের সু-শিক্ষার জন্য সমাজ গুরুত্বপূর্ণ প্লাটফরম। সন্তানকে সময় দিন, সে যাতে সমাজকে বুঝতে পারে। সন্তানের সাথে মাঝে মাঝে বিভিন্ন ধরনের বিনোদনে অংশ নিন। সমাজের অসঙ্গগতিগুলো নিয়ে কথা বলুন। তার নিজের দেশের এবং পৃথিবীর সমস্যাগুলো নিয়ে তার সাথে আলোচনা করুন। আলোচনা করুন সমাজে ও পরিবারে তার দায়িত্ব নিয়ে। একজন শিক্ষার্থী বিদ্যালয়ের নিয়ন্ত্রনে যতক্ষণ থাকে পরিবারের নিয়ন্ত্রনে থাকে তার চেয়ে তিনগুন সময়। এই সময়টা সে কিভাবে ব্যবহার করছে তার উপরই নির্ভর করে তার সামগ্রিক বিকাশ। যেকোন পরীক্ষায় জি.পি.এ পাঁচ (৫.০০) প্রাপ্তিই সর্বোচ্চ প্রাপ্তি নয়, শিক্ষার্থীর শারীরিক, মানসিক এবং আবেগিক বিকাশ জরুরী। বিংশ শতাব্দীর চ্যালেঞ্জ মোকাবেলা করে একটি ডিজিটাল এবং সুন্দর বাংলাদেশ নির্মানের যে স্বপ্ন আমরা দেখছি তা বাস্তবায়নে পাঠ্যপুস্তকের জ্ঞান অর্জনের সাথে সাথে কো-কারিকুলাম সংক্রান্ত কাজও গুরুত্ব সহকারে আয়ত্ব করতে হবে। আসুন বিদ্যালয় এবং অভিভাবকবৃন্দের পরিকল্পিত প্রচেষ্টার মাধ্যমে আমরা আগামী প্রজন্মকে গড়ে তুলতে অংশ গ্রহণ করি এবং নির্মান করি সমৃদ্ধশালী বাংলাদেশ এবং বাসযোগ্য পৃথিবী। পরম করুনাময়ের কাছে প্রার্থনা সকলের মিলিত প্রয়াসে আমাদের প্রিয় শিক্ষাঙ্গনে শান্তি-শৃঙ্খলা ও শিক্ষার সুষ্ঠু পরিবেশ বজায় রেখে সকলে যেন অভীষ্ঠ লক্ষ্যে পৌঁছাতে পারি।',
                    'image_json' => [
                        'url' => asset('images/teacher.png'),
                        'path' => 'images/teacher.png',
                        'provider' => 'static',
                    ],
                    'row_index' => 1,
                    'column_index' => 1,
                    'colspan' => 1,
                    'is_active' => true,
                ],
                [
                    'name' => 'Chairman',
                    'title' => 'সভাপতির বাণী',
                    'designation' => 'সভাপতি',
                    'speech' => 'আমাদের ওয়েবসাইট প্রস্তুত হচ্ছে  জেনে আমি খুবই আনন্দিত। এর মাধ্যমে প্রতিষ্ঠান পরিচিতি ও সার্বিক কার্যক্রমে গতিশীলতা ও জবাবদিহিতা নিশ্চিত হবে বলে আমি মনে করি। আশা করি, ওয়েবসাইট ডেভেলপমেন্ট কার্যক্রমটি তথ্যবহুল হবে এবং আপডেট থাকবে। ওয়েবসাইট প্রস্তুতকরণের সাথে সংশ্লিষ্ট সবাইকে আমার আন্তরিক ধন্যবাদ ও অভিনন্দন। এ প্রতিষ্ঠানের শিক্ষার্থীরা সঠিক জ্ঞান অর্জনের মাধ্যমে ভবিষ্যতে আলোকিত মানুষ হয়ে দেশ ও জনগণের সেবক হিসেবে গড়ে উঠুক এবং তাদের পথ চলা হোক সত্য, সুন্দর, কল্যাণ ও আলোর পথে। সবার জন্য আমার শুভ কামনা।',
                    'image_json' => [
                        'url' => asset('images/teacher.png'),
                        'path' => 'images/teacher.png',
                        'provider' => 'static',
                    ],
                    'row_index' => 1,
                    'column_index' => 2,
                    'colspan' => 1,
                    'is_active' => true,
                ],
            ];

            foreach ($speechSamples as $sample) {
                $model = Speech::query()->updateOrCreate(
                    [
                        'row_index' => $sample['row_index'],
                        'column_index' => $sample['column_index'],
                    ],
                    $sample
                );

                if ($model->wasRecentlyCreated) {
                    $speechesCreated++;
                } else {
                    $speechesUpdated++;
                }
            }
        });

        return [
            'options' => [
                'created' => $optionsCreated,
                'updated' => $optionsUpdated,
            ],
            'notices' => [
                'created' => $noticesCreated,
                'updated' => $noticesUpdated,
            ],
            'galleries' => [
                'created' => $galleriesCreated,
                'updated' => $galleriesUpdated,
            ],
            'speeches' => [
                'created' => $speechesCreated,
                'updated' => $speechesUpdated,
            ],
            'sliders' => [
                'total' => 2,
                'source' => 'institute.branding.slider_json',
            ],
        ];
    }

    public function isLocked(string $section): bool
    {
        $locks = Option::get('transfer.locks.json', []);
        return (bool) ($locks[$section] ?? false);
    }

    public function lock(string $section): void
    {
        $locks = Option::get('transfer.locks.json', []);
        $locks[$section] = true;
        Option::set('transfer.locks.json', $locks);
    }

    public function unlock(string $section): void
    {
        $locks = Option::get('transfer.locks.json', []);
        $locks[$section] = false;
        Option::set('transfer.locks.json', $locks);
    }

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
        if ($this->isLocked('speeches')) {
            throw new RuntimeException('Speech transfer is locked.');
        }

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

        $this->lock('speeches');

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'options_updated' => $optionsUpdated,
        ];
    }

    public function transferSliderImagesFromWordPress(): array
    {
        if ($this->isLocked('sliders')) {
            throw new RuntimeException('Slider images transfer is locked.');
        }

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

        $this->transferBannerFromWordPress();

        $this->lock('sliders');

        return [
            'transferred' => $transferred,
            'total_source' => $sourceImages->count(),
            'option_updated' => ! empty($sliderJson),
        ];
    }

    public function transferBannerFromWordPress(): array
    {
        $bannerUrl = DB::connection('wordpress')
            ->table('sm_options')
            ->where('option_name', 'logo_upload')
            ->value('option_value');

        if (blank($bannerUrl)) {
            return [
                'transferred' => 0,
                'status' => 'no_source_found',
            ];
        }

        $imageData = $this->downloadAndProcessImage($bannerUrl, 'branding');

        if ($imageData) {
            Option::set('institute.branding.banner_json', $imageData);
            return [
                'transferred' => 1,
                'status' => 'success',
                'path' => $imageData['path'],
            ];
        }

        return [
            'transferred' => 0,
            'status' => 'download_failed',
        ];
    }

    public function transferGalleriesFromWordPress(): array
    {
        if ($this->isLocked('galleries')) {
            throw new RuntimeException('Gallery transfer is locked.');
        }

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

                $contentImageUrl = $this->extractImageUrlFromHtml((string) ($row->post_content ?? ''));
                $attachmentCandidates = $this->getGalleryAttachmentImageCandidates(
                    $legacyId,
                    $contentImageUrl
                );

                $candidates = [];
                if ($contentImageUrl !== null) {
                    $candidates[] = [
                        'legacy_id' => $legacyId,
                        'url' => $contentImageUrl,
                    ];
                }

                foreach ($attachmentCandidates as $candidate) {
                    $candidates[] = [
                        'legacy_id' => (int) ($candidate['legacy_id'] ?? 0),
                        'url' => (string) ($candidate['url'] ?? ''),
                    ];
                }

                $deduplicatedCandidates = [];
                $seenLegacyIds = [];
                $seenUrls = [];
                foreach ($candidates as $candidate) {
                    $candidateLegacyId = (int) ($candidate['legacy_id'] ?? 0);
                    $candidateUrl = trim((string) ($candidate['url'] ?? ''));

                    if ($candidateLegacyId <= 0 || $candidateUrl === '') {
                        continue;
                    }

                    if (isset($seenLegacyIds[$candidateLegacyId]) || isset($seenUrls[$candidateUrl])) {
                        continue;
                    }

                    $seenLegacyIds[$candidateLegacyId] = true;
                    $seenUrls[$candidateUrl] = true;
                    $deduplicatedCandidates[] = [
                        'legacy_id' => $candidateLegacyId,
                        'url' => $candidateUrl,
                    ];
                }

                if ($deduplicatedCandidates === []) {
                    $existing = Gallery::query()->where('legacy_id', $legacyId)->first();
                    if (! $existing) {
                        $skippedNoImage++;
                    }
                    continue;
                }

                foreach ($deduplicatedCandidates as $candidate) {
                    $candidateLegacyId = (int) $candidate['legacy_id'];
                    $candidateUrl = (string) $candidate['url'];

                    $existing = Gallery::query()->where('legacy_id', $candidateLegacyId)->first();
                    $imagePath = $existing?->image_path;

                    $imageData = $this->downloadAndProcessImage($candidateUrl, 'gallery/photos');
                    if ($imageData !== null) {
                        $imagePath = $imageData['path'] ?? $imagePath;
                    } elseif ($existing === null) {
                        $failedImageDownload++;
                        continue;
                    }

                    $gallery = Gallery::updateOrCreate(
                        ['legacy_id' => $candidateLegacyId],
                        [
                            'type' => Gallery::TYPE_PHOTO,
                            'title' => trim((string) ($row->post_name ?? '')),
                            'category' => 'Imported',
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
        if ($this->isLocked('notices')) {
            throw new RuntimeException('Notice transfer is locked.');
        }

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

        $this->lock('notices');

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
        if ($this->isLocked('news')) {
            throw new RuntimeException('News transfer is locked.');
        }

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

        $this->lock('news');

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

    private function getSampleOptionValue(string $key, string $type): mixed
    {
        $staticLogo = [
            'url' => asset('images/default-logo.png'),
            'path' => 'images/default-logo.png',
            'provider' => 'static',
        ];

        return match ($key) {
            'institute.branding.name' => 'New School Name',
            'institute.branding.show_top_header' => 1,
            'institute.branding.logo_json' => $staticLogo,
            'institute.theme.slider_type' => 'slider_only',
            'institute.theme.banner_type' => 'info_only',
            'institute.identity.established_year' => 2001,
            'institute.identity.eiin' => '123456',
            'institute.identity.code' => 'BMS-001',
            'institute.contact.address' => 'Sylhet, Bangladesh',
            'institute.contact.phone' => '+8801700000000',
            'institute.contact.email' => 'info@barnomala.edu.bd',
            'institute.contact.website' => 'https://example.com',
            'institute.contact.map_link' => 'https://maps.google.com/?q=23.8103,90.4125',
            'institute.about.title' => 'About Our Institute',
            'institute.about.side_panel_type' => 'notice',
            'institute.about.button_text' => 'Read More',
            'institute.about.text' => 'আমাদের শিক্ষা প্রতিষ্ঠান একটি ঐতিহ্যবাহী বিদ্যাপীঠ। দীর্ঘ পথচলায় আমরা অসংখ্য মেধাবী শিক্ষার্থী উপহার দিয়েছি যারা দেশ ও দশের কল্যাণে নিয়োজিত। আমাদের লক্ষ্য হলো শিক্ষার্থীদের সুপ্ত প্রতিভা বিকাশে সহায়তা করা এবং তাদের সুনাগরিক হিসেবে গড়ে তোলা।',
            'institute.footer.text' => 'পরিপূর্ণ ডিজিটালাইজেশনে ডায়নামিক ওয়েব সাইট উন্নয়ন চলছে। শীঘ্রই পরিপূর্ণ ওয়েবসাইট দেখতে পাবেন। আশা করি এর মাধ্যমে বিদ্যালয়ের সামগ্রিক ব্যবস্থাপনা পরিপূর্ণ ডিজিটালাইজেশন হবে। এবং সকলেই উপকৃত হবেন।',
            'institute.social.facebook' => '#',
            'institute.social.youtube' => '#',
            'institute.about.image_json' => [
                'url' => asset('images/about-image.webp'),
                'path' => 'images/about-image.webp',
                'provider' => 'static',
            ],
            'institute.homepage.layout' => [
                'hero' => true,
                'latest_news' => true,
                'institute_info' => true,
                'message_section' => true,
                'stats_counter' => true,
                'quick_links' => true,
                'teachers' => false,
                'student_demographics' => true,
                'featured_news' => false,
                'gallery' => true,
                'general_committee' => false,
            ],
            'transfer.locks.json' => [
                'speeches' => false,
                'sliders' => false,
                'galleries' => false,
                'notices' => false,
                'news' => false,
            ],
            'institute.links.important_json' => [
                ['title' => 'Sylhet Education Board', 'url' => 'https://www.sylhetboard.gov.bd'],
                ['title' => 'Directorate of Secondary and Higher Education', 'url' => 'https://www.dshe.gov.bd'],
                ['title' => 'Ministry of Education', 'url' => 'https://moedu.gov.bd'],
                ['title' => 'Bangladesh National Portal', 'url' => 'https://bangladesh.gov.bd'],
                ['title' => 'BANBEIS', 'url' => 'https://www.banbeis.gov.bd'],
                ['title' => 'Teachers Portal', 'url' => 'https://www.teachers.gov.bd'],
            ],
            'institute.stats.classes_count' => 7,
            'institute.stats.students_count' => 1200,
            'institute.stats.teachers_count' => 20,
            'institute.stats.staffs_count' => 4,
            'institute.demographics.classes' => [
                'Six' => 120,
                'Seven' => 110,
                'Eight' => 105,
                'Nine' => 95,
                'Ten' => 80,
            ],
            'institute.demographics.gender' => [
                'Male' => 260,
                'Female' => 250,
                'Other' => 0
            ],
            'institute.demographics.religion' => [
                'Islam' => 430,
                'Hindu' => 80,
                'Christian' => 0,
                'Buddhism' => 0
            ],
            default => match ($type) {
                'json', 'object', 'array' => [],
                'integer', 'int' => 0,
                'float', 'double' => 0.0,
                'boolean', 'bool' => false,
                default => '',
            },
        };
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

    private function getGalleryAttachmentImageCandidates(int $postId, ?string $fallbackMediaUrl = null): array
    {
        if ($postId <= 0) {
            return [];
        }

        if (! Schema::connection('wordpress')->hasTable('sm_postmeta')) {
            return [];
        }

        $metaValues = DB::connection('wordpress')
            ->table('sm_postmeta')
            ->where('post_id', $postId)
            ->pluck('meta_value')
            ->all();

        $attachmentIds = [];
        foreach ($metaValues as $metaValue) {
            $value = trim((string) $metaValue);
            if ($value !== '' && ctype_digit($value)) {
                $attachmentIds[(int) $value] = true;
            }
        }

        $attachmentIds = array_values(array_filter(array_keys($attachmentIds), fn (int $id) => $id > 0));
        if ($attachmentIds === []) {
            return [];
        }

        $attachedRows = DB::connection('wordpress')
            ->table('sm_postmeta')
            ->select('post_id', 'meta_value')
            ->whereIn('post_id', $attachmentIds)
            ->where('meta_key', '_wp_attached_file')
            ->get();

        $candidates = [];
        foreach ($attachedRows as $attachedRow) {
            $attachmentId = (int) ($attachedRow->post_id ?? 0);
            $attachedFile = trim((string) ($attachedRow->meta_value ?? ''));
            if ($attachmentId <= 0 || $attachedFile === '') {
                continue;
            }

            $url = $this->buildWordPressAttachmentUrl($attachedFile, $fallbackMediaUrl);
            if ($url === null) {
                continue;
            }

            $candidates[$attachmentId] = [
                'legacy_id' => $attachmentId,
                'url' => $url,
            ];
        }

        return array_values($candidates);
    }

    private function buildWordPressAttachmentUrl(string $attachedFile, ?string $fallbackMediaUrl = null): ?string
    {
        $attachedFile = trim(html_entity_decode($attachedFile));
        if ($attachedFile === '') {
            return null;
        }

        if (filter_var($attachedFile, FILTER_VALIDATE_URL)) {
            return $attachedFile;
        }

        $baseUrl = null;
        $fallbackMediaUrl = trim((string) ($fallbackMediaUrl ?? ''));
        if ($fallbackMediaUrl !== '' && filter_var($fallbackMediaUrl, FILTER_VALIDATE_URL)) {
            $parts = parse_url($fallbackMediaUrl);
            if (! empty($parts['scheme']) && ! empty($parts['host'])) {
                $baseUrl = $parts['scheme'] . '://' . $parts['host'];
                if (! empty($parts['port'])) {
                    $baseUrl .= ':' . $parts['port'];
                }
            }
        }

        if ($baseUrl === null) {
            $appUrl = trim((string) config('app.url', ''));
            if ($appUrl !== '' && filter_var($appUrl, FILTER_VALIDATE_URL)) {
                $parts = parse_url($appUrl);
                if (! empty($parts['scheme']) && ! empty($parts['host'])) {
                    $baseUrl = $parts['scheme'] . '://' . $parts['host'];
                    if (! empty($parts['port'])) {
                        $baseUrl .= ':' . $parts['port'];
                    }
                }
            }
        }

        if ($baseUrl === null) {
            return null;
        }

        return rtrim($baseUrl, '/') . '/wp-content/uploads/' . ltrim($attachedFile, '/');
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
