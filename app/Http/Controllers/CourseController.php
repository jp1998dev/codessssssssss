<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('prerequisites')->get();
        $allCourses = Course::orderBy('name', 'asc')->get(); // Alphabetical order
        return view('vp_academic.course_management.courses', compact('courses', 'allCourses'));
    }

    public function create()
    {
        $allCourses = Course::orderBy('name', 'asc')->get(); // Alphabetical order
        return view('vp_academic.course_management.courses', compact('allCourses'));
    }

    public function store(Request $request)
    {
        // Validate input
      $validated = $request->validate([
    'code' => 'required|string', 
    'name' => 'required|string',
    'description' => 'nullable|string',
    'units' => 'required|numeric',
    'lecture_hours' => 'nullable|numeric',
    'lab_hours' => 'nullable|numeric',
    'prerequisite_id' => 'nullable|array',
    'prerequisite_id.*' => 'exists:courses,id',
]);


        // pag create
        $course = Course::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'units' => $validated['units'],
            'lecture_hours' => $validated['lecture_hours'],
            'lab_hours' => $validated['lab_hours'],
        ]);

        // kung prerequisites ay selected
        if (!empty($validated['prerequisite_id'])) {
            foreach ($validated['prerequisite_id'] as $prerequisiteId) {
                // pag insert
                DB::table('course_prerequisite')->insert([
                    'course_id' => $course->id,
                    'prerequisite_id' => $prerequisiteId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('courses.index')->with('success', 'Course added successfully!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:courses,code,' . $id,
            'name' => 'required|string',
            'description' => 'nullable|string',
            'units' => 'required|numeric',
            'lecture_hours' => 'nullable|numeric',
            'lab_hours' => 'nullable|numeric',
            'prerequisite_id' => 'nullable|array',
            'prerequisite_id.*' => 'exists:courses,id',
        ]);

        $course = Course::findOrFail($id);
        $course->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'units' => $validated['units'],
            'lecture_hours' => $validated['lecture_hours'],
            'lab_hours' => $validated['lab_hours'],
        ]);

        // Sync prerequisites (replaces all with new ones)
        if (!empty($validated['prerequisite_id'])) {
            DB::table('course_prerequisite')->where('course_id', $course->id)->delete();
            foreach ($validated['prerequisite_id'] as $prerequisiteId) {
                DB::table('course_prerequisite')->insert([
                    'course_id' => $course->id,
                    'prerequisite_id' => $prerequisiteId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } else {
            // No prerequisites selected, clear existing
            DB::table('course_prerequisite')->where('course_id', $course->id)->delete();
        }

        return redirect()->route('courses.index')->with('success', 'Course updated successfully!');
    }



    public function toggleActive($id)
    {
        $course = Course::findOrFail($id);
        $course->active = !$course->active;
        $course->save();

        return redirect()->route('courses.index')->with('success', 'Course status updated.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Course deleted successfully.');
    }

    public function prerequisites(Request $request)
{
    $course = Course::with('prerequisites')->find($request->course_id);

    if (!$course) {
        return response()->json(['prerequisites' => []]);
    }

    return response()->json([
        'prerequisites' => $course->prerequisites->map(function ($prereq) {
            return [
                'id' => $prereq->id,
                'code' => $prereq->code,
                'name' => $prereq->name,
            ];
        })
    ]);
}

}
