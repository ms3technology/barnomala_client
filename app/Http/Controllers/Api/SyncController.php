<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SyncController extends Controller
{
    public function syncOptions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'options' => ['nullable', 'array'],

        ]);

        $optionsPayload = $this->normalizeOptions($validated['options'] ?? []);

        DB::transaction(function () use ($optionsPayload): void {
            $this->upsertOptions($optionsPayload);
        });

        return response()->json([
            'synced' => true,
            'counts' => [
                'options' => count($optionsPayload),
            ],
        ]);
    }

    protected function normalizeOptions(array $options): array
    {
        $rows = [];

        foreach ($options as $key => $value) {
            if (! is_string($key) || $key === '') {
                throw ValidationException::withMessages([
                    'options' => 'Options must be an object keyed by option name.',
                ]);
            }

            [$optionValue, $valueType] = $this->serializeOptionValue($value);

            $rows[] = [
                'option_key' => $key,
                'option_value' => $optionValue,
                'value_type' => $valueType,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $rows;
    }

    protected function upsertOptions(array $rows): void
    {
        Option::query()->upsert(
            $rows,
            ['option_key'],
            ['option_value', 'value_type', 'updated_at']
        );
    }

    protected function serializeOptionValue(mixed $value): array
    {
        return match (true) {
            is_array($value) => [json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'json'],
            is_bool($value) => [$value ? '1' : '0', 'boolean'],
            is_int($value) => [(string) $value, 'integer'],
            $value === null => [null, 'string'],
            default => [(string) $value, 'string'],
        };
    }
}
