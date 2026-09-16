<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Committee;
use App\Models\CommitteeMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommitteeSyncController extends Controller
{
    public function sync(Request $request)
    {
        $committeesData = $request->input('committees', []);
        
        if (!is_array($committeesData)) {
            $committeesData = json_decode($request->getContent(), true)['committees'] ?? [];
        }

        $summary = [
            'updated' => 0,
            'deleted' => 0,
            'failed' => 0
        ];

        try {
            DB::beginTransaction();
            foreach ($committeesData as $item) {
                if (!isset($item['id'])) {
                    $summary['failed']++;
                    continue;
                }

                $legacyId = $item['id'];

                // If body has only id, delete it
                if (count($item) === 1) {
                    $deleted = Committee::where('legacy_id', $legacyId)->delete();
                    if ($deleted) $summary['deleted']++;
                    continue;
                }

                // Map data from request to Committee model attributes.
                //
                // `legacy_id` is the stable primary identifier from the
                // source system, so we match on that for upserts. Backfill
                // it for rows previously imported before the column existed.
                $existing = Committee::where('legacy_id', $legacyId)->first();
                if (!$existing) {
                    // Pre-migration rows may still be keyed by the local PK
                    // equal to the legacy id; migrate them on the fly so
                    // future syncs stop creating duplicates.
                    $existing = Committee::where('id', $legacyId)
                        ->whereNull('legacy_id')
                        ->first();
                    if ($existing) {
                        $existing->legacy_id = $legacyId;
                    }
                }

                $data = [
                    'legacy_id' => $legacyId,
                    'name' => $item['name'] ?? 'Unknown',
                    'session' => $item['session'] ?? null,
                    'description' => $item['description'] ?? null,
                    'order_index' => $item['order_index'] ?? 0,
                    'status' => $item['status'] ?? 'active',
                    'note' => $item['note'] ?? null,
                ];

                if ($existing) {
                    $existing->fill($data)->save();
                    $committee = $existing;
                } else {
                    $committee = Committee::create($data);
                }

                // Sync members if provided. Whitelist only known fillable fields
                // so unknown payload keys (e.g. nested relations) cannot leak in.
                if (isset($item['members']) && is_array($item['members'])) {
                    foreach ($item['members'] as $memberItem) {
                        if (!isset($memberItem['id'])) continue;

                        $memberLegacyId = $memberItem['id'];
                        $memberExisting = CommitteeMember::where('legacy_id', $memberLegacyId)->first();
                        if (!$memberExisting) {
                            $memberExisting = CommitteeMember::where('id', $memberLegacyId)
                                ->whereNull('legacy_id')
                                ->first();
                            if ($memberExisting) {
                                $memberExisting->legacy_id = $memberLegacyId;
                            }
                        }

                        $memberData = array_intersect_key($memberItem, array_flip([
                            'name',
                            'designation',
                            'father_name',
                            'mother_name',
                            'phone',
                            'email',
                            'photo',
                            'order_index',
                            'joining_date',
                            'leaving_date',
                            'is_active',
                        ])) + [
                            'committee_id' => $committee->id,
                            'legacy_id' => $memberLegacyId,
                        ];

                        if ($memberExisting) {
                            $memberExisting->fill($memberData)->save();
                        } else {
                            CommitteeMember::create($memberData);
                        }
                    }
                }

                $summary['updated']++;
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'summary' => $summary
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Committee Sync Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Sync failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
