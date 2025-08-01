<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admission;
use App\Models\OldAccount;
use App\Models\Student;
use Error;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('query', '');

        $activeSY = DB::table('school_years')
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$activeSY) {
            return response()->json([]);
        }

        // Get matching students (no filtering by billing yet)
        $students = Admission::where(function ($q) use ($query) {
            $q->where('student_id', 'like', "%$query%")
                ->orWhere('first_name', 'like', "%$query%")
                ->orWhere('last_name', 'like', "%$query%");
        })
            ->with(['billing' => function ($q) use ($activeSY) {
                $q->where('school_year', $activeSY->name)
                    ->where('semester', $activeSY->semester);
            }])
            ->get()
            ->map(function ($student) {
                return [
                    'student_id'  => $student->student_id,
                    'full_name'   => $student->getFullNameAttribute(),
                    'balance_due' => optional($student->billing)->balance_due ?? 0,
                    'school_year' => optional($student->billing)->school_year ?? '',
                    'semester'    => optional($student->billing)->semester ?? '',
                ];
            });
        // Log the raw student data
        Log::info('Search triggered for query: ' . $query, [
            'students' => $students->toArray()
        ]);

        return response()->json($students);
    }
    public function shsSearch(Request $request)
    {
        $query = $request->get('query', '');

        $activeSY = DB::table('school_years')
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$activeSY) {
            return response()->json([]);
        }

        // Get matching students (no filtering by billing yet)
        $students = Student::where(function ($q) use ($query) {
            $q->where('student_id', 'like', "%$query%")
                ->orWhere('first_name', 'like', "%$query%")
                ->orWhere('last_name', 'like', "%$query%");
        })
            ->with(['billing' => function ($q) use ($activeSY) {
                $q->where('school_year', $activeSY->name)
                    ->where('semester', $activeSY->semester);
            }])
            ->get()
            ->map(function ($student) {
                return [
                    'student_id'  => $student->student_id,
                    'student_lrn' => $student->lrn_number,
                    'full_name'   => $student->getFullNameAttribute(),
                    'balance_due' => optional($student->billing)->balance_due ?? 0,
                    'school_year' => optional($student->billing)->school_year ?? '',
                    'semester'    => optional($student->billing)->semester ?? '',
                ];
            });

        return response()->json($students);
    }

    public function oldAccountSearch(Request $request)
    {
        $query = $request->get('query', '');

        $activeSY = DB::table('school_years')
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$activeSY) {
            return response()->json([]);
        }

        // Get matching students (no filtering by billing yet)
        $students = OldAccount::where(function ($q) use ($query) {
            $q->where('name', 'like', "%$query%");
        })
            ->get()
            ->map(function ($student) {
                return [
                    'student_id'  => $student->id,
                    'name' => $student->name,
                    'balance' => $student->balance ?? 0,
                    'year_graduated' => $student->year_graduated ?? '',
                    'course_strand'    => $student->course_strand ?? '',
                ];
            });

        return response()->json($students);
    }
    public function searchAllStudents(Request $request)
    {
        try {
            $query = $request->get('query', '');
            $shsStudents = $this->searchShs($query);
            $admissions = $this->searchAdmission($query);

            return response()->json(array_merge($shsStudents, $admissions));
        } catch (\Exception $e) {
            Log::error("Search error", [
                'message' => $e->getMessage(),
                // 'trace'   => $e->getTraceAsString()
            ]);
            return response()->json([]);
        }
    }

    private function searchShs($query)
    {
        try {
            $query = $query ?: '';
            $activeSY = DB::table('school_years')
                ->where('is_active', 1)
                ->whereNull('deleted_at')
                ->first();

            if (!$activeSY) {
                return [];
            }

            $students = Student::where(function ($q) use ($query) {
                $q->where('student_id', 'like', "%$query%")
                    ->orWhere('first_name', 'like', "%$query%")
                    ->orWhere('last_name', 'like', "%$query%");
            })
                ->whereHas('billing', function ($q) use ($activeSY) {
                    $q->where('school_year', $activeSY->name)
                        ->where('semester', $activeSY->semester);
                })
                ->with(['billing' => function ($q) use ($activeSY) {
                    $q->where('school_year', $activeSY->name)
                        ->where('semester', $activeSY->semester);
                }])
                ->with(['enrollment.strand'])
                ->get()
                ->map(function ($student) {
                    return [
                        'student_id'  => $student->student_id,
                        'student_lrn' => $student->lrn_number,
                        'full_name'   => $student->getFullNameAttribute(),
                        'balance_due' => optional($student->billing)->balance_due ?? 0,
                        'school_year' => optional($student->billing)->school_year ?? '',
                        'semester'    => optional($student->billing)->semester ?? '',
                        'course'      => $student->enrollment->strand,
                    ];
                });

            return $students->toArray();
        } catch (\Exception $e) {
            throw $e;
        }
    }
    private function searchAdmission($query)
    {
        try {
            $query = $query ?: '';

            $activeSY = DB::table('school_years')
                ->where('is_active', 1)
                ->whereNull('deleted_at')
                ->first();

            if (!$activeSY) {
                return response()->json([]);
            }

            $admissions = Admission::where(function ($q) use ($query) {
                $q->where('student_id', 'like', "%$query%")
                    ->orWhere('first_name', 'like', "%$query%")
                    ->orWhere('last_name', 'like', "%$query%");
            })
                ->with(['billing' => function ($q) use ($activeSY) {
                    $q->where('school_year', $activeSY->name)
                        ->where('semester', $activeSY->semester);
                }])
                ->with(['courseMapping.program'])
                ->get()
                ->map(function ($admission) {
                    return [
                        'student_id'  => $admission->student_id,
                        'full_name'   => $admission->getFullNameAttribute(),
                        'balance_due' => optional($admission->billing)->balance_due ?? 0,
                        'school_year' => optional($admission->billing)->school_year ?? '',
                        'semester'    => optional($admission->billing)->semester ?? '',
                        'course'      => optional(optional($admission->courseMapping)->program)->code ?? '',
                    ];
                });

            return $admissions->toArray();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
