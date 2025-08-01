<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\OldAccPayment;
use App\Models\OtherPayment;
use App\Models\Payment;
use App\Models\SchoolYear;
use App\Models\ShsPayment;
use App\Models\UniformPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DataController extends Controller
{
    public function getDailyCollection(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
            ]);

            $date = $request->date;

            $collections = [
                'shsCollection' => [
                    'amount' => ShsPayment::where(function ($query) {
                        $query->whereRaw('LOWER(payment_method) = ?', ['cash'])
                            ->orWhereNull('payment_method');
                    })
                        ->whereDate('updated_at', $date)
                        ->sum('amount'),
                    'count'  => ShsPayment::where(function ($query) {
                        $query->whereRaw('LOWER(payment_method) = ?', ['cash'])
                            ->orWhereNull('payment_method');
                    })
                        ->whereDate('updated_at', $date)
                        ->count(),
                ],
                'collegeCollection' => [
                    'amount' => Payment::where(function ($query) {
                        $query->whereRaw('LOWER(payment_method) = ?', ['cash'])
                            ->orWhereNull('payment_method');
                    })
                        ->whereDate('updated_at', $date)
                        ->sum('amount'),
                    'count'  => Payment::where(function ($query) {
                        $query->whereRaw('LOWER(payment_method) = ?', ['cash'])
                            ->orWhereNull('payment_method');
                    })
                        ->whereDate('updated_at', $date)
                        ->count(),
                ],

                'oldAccountCollection' => [
                    'amount' => OldAccPayment::whereDate('updated_at', $date)->sum('amount'),
                    'count'  => OldAccPayment::whereDate('updated_at', $date)->count(),
                ],
                'otherFeeCollection' => [
                    'amount' => OtherPayment::where(function ($query) {
                        $query->where('payment_method', '!=', 'ONLINE')
                            ->orWhereNull('payment_method');
                    })
                        ->whereDate('updated_at', $date)
                        ->sum('amount'),
                    'count'  => OtherPayment::where(function ($query) {
                        $query->where('payment_method', '!=', 'ONLINE')
                            ->orWhereNull('payment_method');
                    })
                        ->whereDate('updated_at', $date)
                        ->count(),
                ],

                'uniformCollection' => [
                    'amount' => UniformPayment::whereDate('updated_at', $date)->sum('amount'),
                    'count'  => UniformPayment::whereDate('updated_at', $date)->count(),
                ],
            ];

            return response()->json($collections);
        } catch (\Exception $e) {
            Log::error("Daily Collection error: " . $e->getMessage());
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function getSemesterCollection(Request $request)
    {
        try {
            $request->validate([
                'semester_id' => 'required',
            ]);

            $semester = SchoolYear::find($request->semester_id);
            $semesterYear = $semester->name;
            $semesterName = $semester->semester;
            $collections = [
                'shsCollection' => [
                    'amount' => ShsPayment::where('school_year', $semesterYear)
                        ->where('semester', $semesterName)
                        ->sum('amount'),
                    'count'  => ShsPayment::where('school_year', $semesterYear)
                        ->where('semester', $semesterName)
                        ->count(),
                ],
                'collegeCollection' => [
                    'amount' => Payment::where('school_year', $semesterYear)
                        ->where('semester', $semesterName)
                        ->sum('amount'),
                    'count'  => Payment::where('school_year', $semesterYear)
                        ->where('semester', $semesterName)
                        ->count(),
                ],
                'oldAccountCollection' => [
                    'amount' => OldAccPayment::where('school_year', $semesterYear)
                        ->where('semester', $semesterName)
                        ->sum('amount'),
                    'count'  => OldAccPayment::where('school_year', $semesterYear)
                        ->where('semester', $semesterName)
                        ->count(),
                ],
                'otherFeeCollection' => [
                    'amount' => OtherPayment::where('school_year', $semesterYear)
                        ->where('semester', $semesterName)
                        ->sum('amount'),
                    'count'  => OtherPayment::where('school_year', $semesterYear)
                        ->where('semester', $semesterName)
                        ->count(),
                ],
                'uniformCollection' => [
                    'amount' => UniformPayment::where('school_year', $semesterYear)
                        ->where('semester', $semesterName)
                        ->sum('amount'),
                    'count'  => UniformPayment::where('school_year', $semesterYear)
                        ->where('semester', $semesterName)
                        ->count(),
                ],
            ];

            return response()->json($collections);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function getCollegePaymentCollection(Request $request)
    {
        try {
            $payments = Payment::with('student.courseMapping.program')->get();


            $missing = $payments->filter(function ($payment) {
                return !($payment->student && $payment->student->courseMapping && $payment->student->courseMapping->program);
            });

            if ($missing->isNotEmpty()) {
                Log::warning('Missing program relationship for payments: ' . $missing->pluck('id')->join(', '));
            }

            $collections = $payments
                ->filter(function ($payment) {
                    return optional(optional(optional($payment->student)->courseMapping)->program)->name;
                })
                ->groupBy(function ($payment) {
                    return optional(optional(optional($payment->student)->courseMapping)->program)->name;
                })
                ->map(function ($group, $programName) {
                    return [
                        'program' => $programName,
                        'total_amount' => $group->sum('amount'),
                    ];
                })
                ->values();

            return response()->json([
                'collections' => $collections
            ]);
        } catch (\Exception $e) {
            Log::error('getCollegePaymentCollection error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }


    public function getShsPaymentCollection(Request $request)
    {
        try {
            $payments = ShsPayment::with('enrollment.strand')->get();

            $collections = $payments
                ->filter(function ($payment) {
                    return optional($payment->enrollment->strand);
                })
                ->groupBy(function ($payment) {
                    return $payment->enrollment->strand;
                })
                ->map(function ($group, $programName) {
                    return [
                        'program' => $programName,
                        'total_amount' => $group->sum('amount'),
                    ];
                })
                ->values();

            return response()->json([
                'collections' => $collections,

            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }
}
