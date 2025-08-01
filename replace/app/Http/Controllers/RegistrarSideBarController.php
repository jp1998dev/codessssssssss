<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Billing;
use App\Models\ProgramCourseMapping;
use App\Models\RefRegion;
use App\Models\Scholarship;
use App\Models\SchoolYear;
use App\Models\ShsBilling;
use App\Models\ShsEnrollment;
use App\Models\Strand;
use App\Models\Student;
use App\Models\TypeOfPee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegistrarSideBarController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        return view('registrar.registrar_db');
    }

    // Enrollment
    public function newEnrollment()
    {
        // Fetch admissions data
        $admissions = $this->getAdmissionsForActiveSchoolYear();

        // Fetch course mappings
        $courseMappings = $this->getUniqueSortedCourseMappings();

        // Fetch all courses
        $allCourses = $this->getAllCourses();

        // Fetch scholarships
        $scholarships = $this->getActiveScholarships();

        // Calculate total units if a mapping is selected
        $selectedMappingId = request('selected_mapping_id');
        $totalUnits = $this->calculateTotalUnits($selectedMappingId);

        // Fetch regions
        $regions = $this->getRegions();

        // Pass all data to view
        return view('registrar.enrollment.enrollment', compact(
            'admissions',
            'courseMappings',
            'allCourses',
            'scholarships',
            'selectedMappingId',
            'regions',
            'totalUnits'
        ));
    }


    public function newShsEnrollment()
    {
        // Fetch admissions data
        $admissions = $this->getShsForActiveSchoolYear();
        // echo $admissions;
        // Fetch course mappings
        $courseMappings = $this->getUniqueSortedCourseMappings();

        // Fetch all courses
        $allCourses = $this->getAllCourses();

        // Fetch scholarships
        $scholarships = $this->getActiveScholarships();

        // Calculate total units if a mapping is selected
        $selectedMappingId = request('selected_mapping_id');
        $totalUnits = $this->calculateTotalUnits($selectedMappingId);

        // Fetch regions
        $regions = $this->getRegions();

        // Pass all data to view
        $students = Student::all();
        return view('registrar.enrollment.shs-enrollment', compact(
            'admissions',
            'courseMappings',
            'allCourses',
            'scholarships',
            'selectedMappingId',
            'regions',
            'totalUnits',
            'students'
        ));
    }

    private function getShsForActiveSchoolYear()
    {
        $activeSchoolYear = SchoolYear::where('is_active', 1)->first();

        if (!$activeSchoolYear) {
            return response()->json(['error' => 'No active school year found'], 404);
        }

        return Student::with([
            'billing',
            'enrollment.strand',
            'enrollment.gradeLevel',
            'enrollment'
        ])->whereHas('enrollment', function ($query) use ($activeSchoolYear) {
            $query->where('school_year', $activeSchoolYear->name);
        })->get();
    }

    private function getAdmissionsForActiveSchoolYear()
    {
        $activeSchoolYear = \App\Models\SchoolYear::where('is_active', 1)->first();

        if (!$activeSchoolYear) {
            return response()->json(['error' => 'No active school year found'], 404);
        }

        return Admission::with([
            'courseMapping.program',
            'courseMapping.yearLevel',
            'courseMapping.semester',
            'billing'
        ])->where('school_year', $activeSchoolYear->name)
            ->where('semester', $activeSchoolYear->semester)
            ->get();
    }

    private function getUniqueSortedCourseMappings()
    {
        return ProgramCourseMapping::with(['program', 'yearLevel', 'semester'])
            ->get()
            ->groupBy(function ($item) {
                return $item->program_id . '-' . $item->year_level_id . '-' . $item->semester_id . '-' . $item->effective_sy;
            })
            ->map(function ($group) {
                return $group->first();
            })
            ->sortBy(fn($mapping) => $mapping->program->name ?? '');
    }

    private function getAllCourses()
    {
        return \App\Models\Course::orderBy('id')->get();
    }

    private function getActiveScholarships()
    {
        return Scholarship::where('status', 1)->orderBy('name')->get();
    }

    private function calculateTotalUnits($selectedMappingId)
    {
        if (!$selectedMappingId) {
            return 0;
        }

        $selectedMapping = ProgramCourseMapping::find($selectedMappingId);

        if (!$selectedMapping) {
            return 0;
        }

        $matchingMappings = ProgramCourseMapping::where('program_id', $selectedMapping->program_id)
            ->where('year_level_id', $selectedMapping->year_level_id)
            ->where('semester_id', $selectedMapping->semester_id)
            ->where('effective_sy', $selectedMapping->effective_sy)
            ->get();

        $courseIds = $matchingMappings->pluck('course_id')->unique();

        $courses = \App\Models\Course::whereIn('id', $courseIds)->get();
        $totalUnits = 0;

        foreach ($courses as $course) {
            $rawName = strtolower($course->code . ' ' . $course->name . ' ' . $course->description);
            $rawName = preg_replace('/- ?[a-z0-9 &()]+/', '', $rawName);

            $isNSTP = str_contains($rawName, 'national service training program') ||
                str_contains($rawName, 'civic welfare training service') ||
                str_contains($rawName, 'lts/cwts/rotc') ||
                str_contains($rawName, 'lts/rotc');

            $units = floatval($course->units);

            if ($isNSTP) {
                $totalUnits += ($units / 2);  // Divide NSTP/CWTS units
            } else {
                $totalUnits += $units;
            }
        }

        return $totalUnits;
    }
    private function getEnrollmentsForActiveSchoolYear()
    {
        $activeSchoolYear = \App\Models\SchoolYear::where('is_active', 1)->first();

        if (!$activeSchoolYear) {
            return response()->json(['error' => 'No active school year found'], 404);
        }

        return \App\Models\Enrollment::with([
            'courseMapping.program',
            'courseMapping.yearLevel',
            'courseMapping.semester',
            'billing',
            'admission'
        ])->where('school_year', $activeSchoolYear->name)
            ->where('semester', $activeSchoolYear->semester)
            ->get();
    }


    private function getRegions()
    {
        return RefRegion::with('provinces.cities.barangays')->get();
    }


    public function transfereeEnrollment()
    {
        // Fetch admissions data
        $admissions = $this->getAdmissionsForActiveSchoolYear();

        // Fetch course mappings
        $courseMappings = $this->getUniqueSortedCourseMappings();

        // Fetch all courses
        $allCourses = $this->getAllCourses();

        // Fetch scholarships
        $scholarships = $this->getActiveScholarships();

        // Calculate total units if a mapping is selected
        $selectedMappingId = request('selected_mapping_id');
        $totalUnits = $this->calculateTotalUnits($selectedMappingId);

        // Fetch regions
        $regions = $this->getRegions();

        // Pass all data to view
        return view('registrar.enrollment.transferee_enrollment', compact(
            'admissions',
            'courseMappings',
            'allCourses',
            'scholarships',
            'selectedMappingId',
            'regions',
            'totalUnits'
        ));
    }

    public function reEnrollRegular()
    {
        // Fetch admissions data with enrollment_type = 'old'
        $admissions = $this->getEnrollmentsForActiveSchoolYear()->where('enrollment_type', 'old');

        // Fetch course mappings
        $courseMappings = $this->getUniqueSortedCourseMappings();

        // Fetch all courses
        $allCourses = $this->getAllCourses();

        // Fetch scholarships
        $scholarships = $this->getActiveScholarships();

        // Calculate total units if a mapping is selected
        $selectedMappingId = request('selected_mapping_id');
        $totalUnits = $this->calculateTotalUnits($selectedMappingId);

        // Fetch regions
        $regions = $this->getRegions();

        // Pass all data to view
        return view('registrar.enrollment.re_enroll_regular', compact(
            'admissions',
            'courseMappings',
            'allCourses',
            'scholarships',
            'selectedMappingId',
            'regions',
            'totalUnits'
        ));
    }

    public function reEnrollIrregular()
    {
        return view('registrar.enrollment.re_enroll_irregular'); // View for irregular re-enrollment
    }


    public function enrollmentRecords()
    {
        return view('registrar.enrollment.records'); // Table view by student/term
    }

    // Records
    public function quickSearch() {}

    public function bulkUpload()
    {
        return view('registrar.records.bulk_upload');
    }

    // Requests
    public function expressProcessing()
    {
        return view('registrar.requests.express_processing');
    }

    public function notifyStudent()
    {
        return view('registrar.requests.notify_student');
    }

    // Archive
    public function oldStudentRecords()
    {
        return view('registrar.archive.old_student_records');
    }

    public function disposalLog()
    {
        return view('registrar.archive.disposal_log');
    }
     public function editStudent($student_id)
    {
        // Get the currently active school year and semester
        $activeSchoolYear = SchoolYear::where('is_active', 1)->first();

        if (!$activeSchoolYear) {
            Log::error('No active school year found');
            abort(404, 'No active school year found');
        }

        $currentSchoolYear = $activeSchoolYear->name;
        $currentSemester = $activeSchoolYear->semester;

        // Fetch ANY admission record (ignore school_year/semester)
        $admission = Admission::with('scholarship')
            ->where('student_id', $student_id)
            ->latest() // Optional: Get the most recent admission
            ->first();

        if (!$admission) {
            Log::warning('No admission record found for student', ['student_id' => $student_id]);
            abort(404, 'Student has no admission record.');
        }

        // For other tables (billings, etc.), enforce current SY/semester
        $currentTuitionFee = 0;
        $billing = Billing::where('student_id', $student_id)
            ->where('school_year', $currentSchoolYear)
            ->where('semester', $currentSemester)
            ->latest()
            ->first();

        if ($billing) {
            $currentTuitionFee = $billing->tuition_fee;
            Log::debug('Billing record found', [
                'billing_id' => $billing->id,
                'tuition_fee' => $billing->tuition_fee
            ]);
        } else {
            Log::debug('No billing record found for current SY/semester', [
                'student_id' => $student_id
            ]);
        }

        // Continue with the rest of your logic (scholarships, course mappings, etc.)
        $scholarships = Scholarship::all();

        $courseMappings = ProgramCourseMapping::with(['program', 'yearLevel', 'semester'])
            ->get()
            ->groupBy(function ($item) {
                return $item->program_id . '-' . $item->year_level_id . '-' . $item->semester_id . '-' . $item->effective_sy;
            });

        $regions = RefRegion::all();

        return view('registrar.enrollment.edit-students', compact(
            'admission',
            'scholarships',
            'courseMappings',
            'regions',
            'currentTuitionFee',
            'currentSchoolYear',
            'currentSemester'
        ));
    }
    public function editShs($student_id)
    {

        $activeSchoolYear = SchoolYear::where('is_active', 1)->firstOrFail();

        $currentSchoolYear = $activeSchoolYear->name;
        $currentSemester = $activeSchoolYear->semester;


        $student = Student::findOrFail($student_id);


        $enrollment = ShsEnrollment::where('student_id', $student_id)
            ->where('school_years', $currentSchoolYear)
            ->where('semester', $currentSemester)
            ->first();


        $billing = ShsBilling::where('student_lrn', $student->lrn_number)
            ->where('school_year', $currentSchoolYear)
            ->where('semester', $currentSemester)
            ->first();
        $strands = Strand::all();
        $typesOfPee = TypeOfPee::all();

        return view('registrar.enrollment.edit-shs', compact(
            'student',
            'enrollment',
            'billing',
            'strands',
            'typesOfPee',
            'currentSchoolYear',
            'currentSemester'
        ));
    }
}
