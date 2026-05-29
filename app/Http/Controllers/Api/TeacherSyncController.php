<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherSyncController extends Controller
{
    public function sync(Request $request)
    {
        $teachersData = $request->input('teachers', []);
        
        if (!is_array($teachersData)) {
            $teachersData = json_decode($request->getContent(), true)['teachers'] ?? [];
        }

        $summary = [
            'updated' => 0,
            'deleted' => 0,
            'failed' => 0
        ];

        try {
            DB::beginTransaction();
            foreach ($teachersData as $teacher) {
                if (!isset($teacher['id'])) {
                    $summary['failed']++;
                    continue;
                }

                $legacyId = $teacher['id'];
                
                // If body has only id, delete it
                if (count($teacher) === 1) {
                    $deleted = Teacher::where('teacher_legacy_id', $legacyId)->delete();
                    if ($deleted) $summary['deleted']++;
                    continue;
                }

                // Map data from request to teacher model attributes
                $data = [
                    'teacher_name' => $teacher['teacher_name'] ?? 'Unknown',
                    'designation' => $teacher['designation'] ?? null,
                    'department' => $teacher['department'] ?? null,
                    'father_name' => $teacher['father_name'] ?? null,
                    'mother_name' => $teacher['mother_name'] ?? null,
                    'blood_group' => $teacher['blood_group'] ?? null,
                    'religion' => $teacher['religion'] ?? null,
                    'present_address' => $teacher['present_address'] ?? null,
                    'permanent_address' => $teacher['permanent_address'] ?? null,
                    'gender' => $teacher['gender'] ?? null,
                    'priority_index' => $teacher['priority_index'] ?? 0,
                    'photo' => $this->resolvePhotoUrl($teacher['photo'] ?? null),
                    'teacher_code' => $teacher['teacher_code'] ?? null,
                    'phone' => $teacher['phone'] ?? null,
                    'email' => $teacher['email'] ?? null,
                    'joining_date' => $teacher['joining_date'] ?? null,
                    'experience_years' => $teacher['experience_years'] ?? 0,
                    'mpo' => $teacher['mpo'] ?? null,
                    'status' => $teacher['status'] ?? true,
                ];

                Teacher::updateOrCreate(
                    ['teacher_legacy_id' => $legacyId],
                    $data
                );
                
                $summary['updated']++;
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'summary' => $summary
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Teacher Sync Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Sync failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function resolvePhotoUrl(String $photoPath)
    {
        if (!$photoPath) {
            return null;
        }

        if (filter_var($photoPath, FILTER_VALIDATE_URL)) {
            return $photoPath;
        }

        return 'https://cloud.barnomala.com/storage/' . ltrim($photoPath, '/');
    }
}
