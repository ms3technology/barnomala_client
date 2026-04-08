<?php

namespace App\Services\DataTransfer;

use App\Models\Option;
use App\Models\Speech;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class WordPressTransferService
{
    public function getExportResources(): array
    {
        return [
            'students',
            'student/enrollments',
            'subjects',
            'users',
            'teachers',
            'exams',
            'exams/schedules',
            'exams/results',
            'slider-images',
            'committees',
            'governing-body',
            'options',
            'speeches',
        ];
    }

    public function getExportPayload(string $resource): array
    {
        return match ($resource) {
            'students' => $this->exportStudents(),
            'student/enrollments' => $this->exportStudentEnrollments(),
            'subjects' => $this->exportSubjects(),
            'users' => $this->exportUsers(),
            'teachers' => $this->exportTeachers(),
            'exams' => $this->exportExams(),
            'exams/schedules' => $this->exportExamSchedules(),
            'exams/results' => $this->exportExamResults(),
            'slider-images' => $this->exportSliderImages(),
            'committees' => $this->exportCommittees(),
            'governing-body' => $this->exportGoverningBody(),
            'options' => $this->exportOptions(),
            'speeches' => $this->exportSpeeches(),
            default => throw new RuntimeException('Unsupported export resource: '.$resource),
        };
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

                $speech = Speech::updateOrCreate(
                    [
                        'row_index' => $payload['row_index'],
                        'column_index' => $payload['column_index'],
                    ],
                    [
                        'name' => $payload['name'],
                        'title' => $payload['title'],
                        'speech' => $payload['speech'],
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

    public function exportStudents(): array
    {
        return ['success' => true, 'count' => 0, 'data' => []];
    }

    public function exportStudentEnrollments(): array
    {
        return ['success' => true, 'count' => 0, 'data' => []];
    }

    public function exportSubjects(): array
    {
        return ['success' => true, 'count' => 0, 'data' => []];
    }

    public function exportUsers(): array
    {
        $users = User::query()->get();

        $data = $users->map(function (User $user): array {
            return [
                'ID' => $user->id,
                'user_login' => $user->email,
                'user_nicename' => $user->name,
                'user_email' => $user->email,
                'user_url' => null,
                'user_registered' => $user->created_at,
                'display_name' => $user->name,
                'roles' => [],
                'first_name' => null,
                'last_name' => null,
                'nickname' => $user->name,
                'description' => null,
            ];
        })->all();

        return ['success' => true, 'count' => count($data), 'data' => $data];
    }

    public function exportTeachers(): array
    {
        $rows = Teacher::query()
            ->orderBy('priority_index')
            ->orderBy('teacher_name')
            ->get();

        return [
            'success' => true,
            'count' => $rows->count(),
            'data' => $rows->toArray(),
        ];
    }

    public function exportExams(): array
    {
        return ['success' => true, 'count' => 0, 'data' => []];
    }

    public function exportExamSchedules(): array
    {
        return ['success' => true, 'count' => 0, 'data' => []];
    }

    public function exportExamResults(): array
    {
        return ['success' => true, 'count' => 0, 'data' => []];
    }

    public function exportSliderImages(): array
    {
        $sliderJson = Option::get('institute.branding.slider_json', []);
        $items = is_array($sliderJson) ? $sliderJson : [];

        $data = collect($items)->values()->map(function ($item, int $index): array {
            return [
                'id' => $index + 1,
                'image_url' => is_array($item) ? ($item['url'] ?? null) : null,
                'created_at' => null,
            ];
        })->filter(fn ($item) => ! blank($item['image_url']))->values()->all();

        return ['success' => true, 'count' => count($data), 'data' => $data];
    }

    public function exportCommittees(): array
    {
        return ['success' => true, 'count' => 0, 'data' => []];
    }

    public function exportGoverningBody(): array
    {
        return ['success' => true, 'count' => 0, 'data' => []];
    }

    public function exportOptions(): array
    {
        $all = Option::query()->get();

        $data = [];
        foreach ($all as $option) {
            $data[$option->option_key] = $option->value;
        }

        return ['success' => true, 'data' => $data];
    }

    public function exportSpeeches(): array
    {
        $rows = Speech::query()
            ->orderBy('row_index')
            ->orderBy('column_index')
            ->orderBy('id')
            ->get();

        return [
            'success' => true,
            'count' => $rows->count(),
            'data' => $rows->toArray(),
        ];
    }

    private function getSpeechSourceMapping(): array
    {
        return [
            'aboutTitelText',
            'aboutUsText',
            'aboutUsMoreBtn',
            'headmasterSpeechTitle',
            'homeHeadmasterTitle',
            'homeHeadmaster',
            'chairmanSpeechTitle',
            'homeChairmanTitle',
            'homeChairman',
        ];
    }

    private function getOptionMapForSpeechTransfer(): array
    {
        return [
            'aboutTitelText' => 'institute.about.title',
            'aboutUsText' => 'institute.about.text',
            'aboutUsMoreBtn' => 'institute.about.button_text',
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
                'row_index' => 1,
                'column_index' => 1,
                'has_source' => $headmasterHasSource,
            ],
            [
                'key' => 'chairman',
                'name' => ($source['homeChairmanTitle'] ?? '') ?: 'Chairman',
                'title' => ($source['chairmanSpeechTitle'] ?? '') ?: 'Chairman Speech',
                'speech' => ($source['homeChairman'] ?? '') ?: '',
                'row_index' => 1,
                'column_index' => 2,
                'has_source' => $chairmanHasSource,
            ],
        ];
    }
}
