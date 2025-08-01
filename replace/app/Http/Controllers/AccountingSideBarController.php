<?php

namespace App\Http\Controllers;

use App\Events\DailyCollectionFetched;
use App\Models\Admission;
use App\Models\BankDeposit;
use App\Models\Billing;
use App\Models\CashierCollection;
use App\Models\Enrollment;
use App\Models\OldAccount;
use App\Models\OldAccPayment;
use App\Models\OtherPayment;
use App\Models\Payment;
use App\Models\Program;
use App\Models\ProgramCourseMapping;
use App\Models\Scholarship;
use App\Models\SchoolYear;
use App\Models\Semester;
use App\Models\ShsBilling;
use App\Models\ShsEnrollment;
use App\Models\ShsPayment;
use App\Models\Strand;
use App\Models\Student;
use App\Models\StudentCourse;
use App\Models\UniformPayment;
use App\Models\User;
use App\Models\YearLevel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function Pest\Laravel\json;

class AccountingSideBarController extends Controller
{
    /**
     * Show the dashboard page.
     */
    public function dashboard()
    {
        $activeSchoolYear = SchoolYear::where('is_active', 1)->first();
        $schoolYearName = $activeSchoolYear->name ?? null;
        $semester = $activeSchoolYear->semester ?? null;

        $totalInitialFees = Billing::where('school_year', $schoolYearName)
            ->where('semester', $semester)
            ->sum('initial_payment');

        $outstandingBalances = Billing::where('school_year', $schoolYearName)
            ->where('semester', $semester)
            ->sum('balance_due');

        $recentPayments = Payment::orderBy('payment_date', 'desc')->take(5)->get();
        $fullPaymentsCount = Billing::where('is_full_payment', true)->count();

        $balanceDistributionData = Billing::selectRaw('MONTHNAME(created_at) as month, SUM(balance_due) as outstanding, SUM(tuition_fee - balance_due) as collected')
            ->groupBy('month')
            ->orderByRaw("STR_TO_DATE(month, '%M') ASC")
            ->get();

        $paymentSourcesData = Payment::selectRaw('remarks, COUNT(*) as count')
            ->groupBy('remarks')
            ->get();

        // 📊 Program-wise Initial Payments and Balances
        $programFinancials = DB::table('billings')
            ->join(DB::raw('enrollments'), DB::raw('CONVERT(billings.student_id USING utf8mb4) COLLATE utf8mb4_unicode_ci'), '=', DB::raw('enrollments.student_id'))
            ->join('program_course_mappings', 'enrollments.course_mapping_id', '=', 'program_course_mappings.id')
            ->join('programs', 'program_course_mappings.program_id', '=', 'programs.id')
            ->where('billings.school_year', $schoolYearName)
            ->where('billings.semester', $semester)
            ->select(
                'programs.name as program_name',
                DB::raw('SUM(billings.initial_payment) as total_initial_payment'),
                DB::raw('SUM(billings.balance_due) as total_balance_due')
            )
            ->groupBy('programs.name')
            ->get();

        $shsData = [];
        $strandTotal = [];
        $gradeLevelTotal = [
            'g11' => 0,
            'g12' => 0
        ];

        $activeSY = SchoolYear::where('is_active', 1)->first();
        $shsEnrollments = ShsEnrollment::where('school_year', $activeSY->name)
            ->where('semester', $activeSY->semester)
            ->get();
        $strads = Strand::all();
        foreach ($strads as $strand) {
            $strandName = $strand->strand_name;
            $g11 = $shsEnrollments->where('strand', $strandName)->where('grade_level', 11)->count();
            $g12 = $shsEnrollments->where('strand', $strandName)->where('grade_level', 12)->count();
            $shsData[] = [
                'name' => $strandName,
                'g11'   => $g11,
                'g12'   => $g12,
            ];
            $strandTotal[$strandName] = $g11 + $g12;
            $gradeLevelTotal['g11'] += $g11;
            $gradeLevelTotal['g12'] += $g12;
        }
        $enrollmentData = Enrollment::with('courseMapping.program', 'courseMapping.yearLevel')
            ->where('school_year', $activeSY->name)
            ->where('semester', $activeSY->semester)
            ->get()
            ->groupBy(function ($enrollment) {
                return $enrollment->courseMapping->program->name ?? 'Unknown';
            })
            ->map(function ($group) {
                return $group->groupBy(function ($enrollment) {
                    return $enrollment->courseMapping->yearLevel->name ?? 'Unknown';
                })->map->count();
            });
        $yearLevels = YearLevel::orderBy('id')->get();
        $topUnpaid = Billing::with('student')
            ->where('school_year', $activeSY->name)
            ->where('semester', $activeSY->semester)
            ->where('balance_due', '>=', 10000)
            ->orderByDesc('balance_due')
            ->take(10)
            ->get();
        $programs = Program::all();
        return view('accountant.accountant_db', [
            'enrollmentData' => $enrollmentData,
            'totalInitialFees' => $totalInitialFees,
            'outstandingBalances' => $outstandingBalances,
            'recentPayments' => $recentPayments,
            'fullPaymentsCount' => $fullPaymentsCount,
            'balanceDistributionData' => $balanceDistributionData,
            'paymentSourcesData' => $paymentSourcesData,
            'programFinancials' => $programFinancials, // 👈 pass to view
            'shsData' => $shsData,
            'topUnpaid' => $topUnpaid,
            'yearLevels' => $yearLevels,
            'programs' => $programs,
            'strandTotal' => $strandTotal,
            'gradeLevelTotal' => $gradeLevelTotal
        ]);
    }



    /**
     * Show the transactions page.
     */
    public function transactions()
    {
        // Fetch payments and admissions
        $payments = Payment::all();

        // Fetch all necessary admissions and related data
        $admissions = Admission::with(['programCourseMapping.program', 'programCourseMapping.yearLevel'])->get();

        // return response()->json($admissions);
        return view('accountant.transactions', compact('payments', 'admissions'));
    }
    public function shsTransactions()
    {
        try {
            $shsPayments = ShsPayment::with([
                'enrollment.student',
                'enrollment.strand',
                'enrollment.gradeLevel'
            ])->latest('payment_date')->get();

            $payments = $shsPayments->map(function ($payment) {
                $enrollment = $payment->enrollment;
                $student = optional($enrollment)->student;

                return (object) [
                    'or_number'      => $payment->or_number ?? null,
                    'payment_date'           => $payment->payment_date,
                    'full_name'      => $student
                        ? trim("{$student->first_name} {$student->middle_name} {$student->last_name}")
                        : null,
                    'strand'         => optional($enrollment)->strand ?? null,
                    'grade_level'    => optional($enrollment)->grade_level ?? null,
                    'remarks'        => $payment->remarks ?? null,
                    'payment_method' => $payment->payment_method ?? null,
                    'amount'         => $payment->amount,
                    'payment_id'     => $payment->payment_id,
                ];
            });

            return view('accountant.shs-transaction', ['payments' => $payments]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function otherTransactions()
    {
        try {
            $colleges = OtherPayment::where('student_id', '!=', null)
                ->with([
                    'college',
                    'college.courseMapping'
                ])
                ->get();

            $collegespayments = $colleges->map(function ($payment) {
                $enrollment = $payment->college->courseMapping;
                $student = optional($payment)->college;

                return (object) [
                    'or_number'      => $payment->or_number ?? null,
                    'payment_date'           => $payment->payment_date,
                    'full_name'      => $student
                        ? trim("{$student->first_name} {$student->middle_name} {$student->last_name}")
                        : null,
                    'course'         => optional($enrollment)->program->code ?? null,
                    'year_level'    => optional($enrollment)->yearLevel->name ?? null,
                    'semester'    => optional($enrollment)->semester->name ?? null,
                    'remarks'        => $payment->remarks ?? null,
                    'amount'         => $payment->amount,
                    'payment_id'     => $payment->id,
                ];
            });

            $shs = OtherPayment::where('lrn_number', '!=', null)
                ->with([
                    'shs',
                    'shs.enrollment'
                ])
                ->get();

            $shspayments = $shs->map(function ($payment) {
                $enrollment = $payment->shs->enrollment;
                $student = optional($payment)->shs;

                return (object) [
                    'or_number'      => $payment->or_number ?? null,
                    'payment_date'           => $payment->payment_date,
                    'full_name'      => $student
                        ? trim("{$student->first_name} {$student->middle_name} {$student->last_name}")
                        : null,
                    'strand'         => optional($enrollment)->strand ?? null,
                    'grade_level'    => optional($enrollment)->grade_level ?? null,
                    'remarks'        => $payment->remarks ?? null,
                    'amount'         => $payment->amount,
                    'payment_id'     => $payment->id,
                ];
            });

            // return response()->json([
            //        'shspayments' => $shspayments,
            //         'collegespayments' => $collegespayments
            // ]);
            return view(
                'accountant.other-transactions',
                [
                    'shspayments' => $shspayments,
                    'collegespayments' => $collegespayments
                ]
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
            // return response()->json($e);
        }
    }
    public function oldTransactions()
    {
        try {
            $oldPayments = OldAccPayment::where('student_id', '!=', null)
                ->with([
                    'student',
                ])
                ->get();

            $payments = $oldPayments->map(function ($payment) {
                $student = optional($payment)->student;

                return (object) [
                    'or_number'      => $payment->or_number ?? null,
                    'payment_date'           => $payment->payment_date,
                    'full_name'      => $student->name,
                    'course_strand'         => optional($student)->course_strand ?? null,
                    'year_graduated'    => optional($student)->year_graduated ?? null,
                    'remarks'        => $payment->remarks ?? null,
                    'amount'         => $payment->amount,
                    'payment_id'     => $payment->id,
                ];
            });
            // return response()->json([
            //     'payments' => $payments
            // ]);
            return view(
                'accountant.old-transactions',
                [
                    'payments' => $payments
                ]
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
            // return response()->json($e);
        }
    }
    public function uniformTransactions()
    {
        try {
            $colleges = UniformPayment::where('student_id', '!=', null)
                ->with([
                    'college',
                    'college.courseMapping'
                ])
                ->get();

            $collegespayments = $colleges->map(function ($payment) {
                $enrollment = $payment->college->courseMapping;
                $student = optional($payment)->college;

                return (object) [
                    'trans_no'      => $payment->trans_no ?? null,
                    'payment_date'           => $payment->payment_date,
                    'full_name'      => $student
                        ? trim("{$student->first_name} {$student->middle_name} {$student->last_name}")
                        : null,
                    'course'         => optional($enrollment)->program->code ?? null,
                    'year_level'    => optional($enrollment)->yearLevel->name ?? null,
                    'semester'    => optional($enrollment)->semester->name ?? null,
                    'remarks'        => $payment->remarks ?? null,
                    'amount'         => $payment->amount,
                    'payment_id'     => $payment->id,
                ];
            });

            $shs = UniformPayment::where('lrn_number', '!=', null)
                ->with([
                    'shs',
                    'shs.enrollment'
                ])
                ->get();

            $shspayments = $shs->map(function ($payment) {
                $enrollment = $payment->shs->enrollment;
                $student = optional($payment)->shs;

                return (object) [
                    'trans_no'      => $payment->trans_no ?? null,
                    'payment_date'           => $payment->payment_date,
                    'full_name'      => $student
                        ? trim("{$student->first_name} {$student->middle_name} {$student->last_name}")
                        : null,
                    'strand'         => optional($enrollment)->strand ?? null,
                    'grade_level'    => optional($enrollment)->grade_level ?? null,
                    'remarks'        => $payment->remarks ?? null,
                    'amount'         => $payment->amount,
                    'payment_id'     => $payment->id,
                ];
            });

            // return response()->json([
            //        'shspayments' => $shspayments,
            //         'collegespayments' => $collegespayments
            // ]);
            return view(
                'accountant.uniform-transactions',
                [
                    'shspayments' => $shspayments,
                    'collegespayments' => $collegespayments
                ]
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
            // return response()->json($e);
        }
    }



    public function getDailyCollectionByCashier(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $queryDate = $request->date;
        $cashierId = $request->cashier_id;

        $cashiers = User::where('role', 'cashier')
            ->when($cashierId, fn($q) => $q->where('id', $cashierId))
            ->get();

        $cashierCount = 0;
        $results = [];

        foreach ($cashiers as $cashier) {
            $cashierCount++;
            $name = $cashier->name;

            $categories = [
                ['model' => Payment::class, 'label' => 'College'],
                ['model' => ShsPayment::class, 'label' => 'Senior High'],
                ['model' => OtherPayment::class, 'label' => 'Other Fees'],
                ['model' => UniformPayment::class, 'label' => 'Uniform Fees'],
            ];

            foreach ($categories as $cat) {
                $isOfflineOnly = in_array($cat['label'], ['College', 'Senior High', 'Other Fees']);

                $query = $cat['model']::whereDate('created_at', $queryDate)
                    ->where('processed_by', $cashier->id);

                // if ($isOfflineOnly) {
                //     $query->where('payment_method', '!=', 'ONLINE');
                // }

                $total = $query->sum('amount');
                $count = $query->count();

                if ($total > 0) {
                    $results[] = [
                        'cashierId' => $cashier->id,
                        'cashier' => $name,
                        'category' => $cat['label'],
                        'count' => $count,
                        'total' => $total,
                    ];
                }
            }
        }

        return response()->json(['data' => $results]);
    }

    public function getBankDeposits(Request $request)
    {
        $deposits = BankDeposit::orderBy('created_at')->get();
        $date = Carbon::today();


        $collegePayments = Payment::whereDate('payment_date', $date)
            ->sum('amount');

        $shsPayments = ShsPayment::whereDate('payment_date', $date)
            ->sum('amount');

        $otherPayments = OtherPayment::whereDate('payment_date', $date)
            ->sum('amount');

        $uniformPayments = UniformPayment::whereDate('payment_date', $date)->sum('amount');
        $oldPayments = OldAccPayment::whereDate('payment_date', $date)->sum('amount');
        $systemTotal = $collegePayments + $shsPayments + $otherPayments + $uniformPayments + $oldPayments;
        $totalBanked = $deposits->sum('total_deposited');
        return view('accountant.bank-deposit', compact('deposits', 'systemTotal', 'totalBanked'));
    }

    public function getDailyCollectionTotalByCashier(Request $request)
    {

        $queryDate = $request->date ?? Carbon::now()->toDateString();
        $cashierId = $request->cashier_id;

        $cashiers = User::where('role', 'cashier')
            ->when($cashierId, fn($q) => $q->where('id', $cashierId))
            ->with([
                'collegePayment' => fn($q) => $q->where('payment_method', '!=', 'ONLINE')->whereDate('payment_date', $queryDate),
                'shsPayment' => fn($q) => $q->where('payment_method', '!=', 'ONLINE')->whereDate('payment_date', $queryDate),
                'otherPayment' => fn($q) => $q->where('payment_method', '!=', 'ONLINE')->whereDate('payment_date', $queryDate),
                'uniformPayment' => fn($q) => $q->whereDate('payment_date', $queryDate),
                'oldAccountPayment' => fn($q) => $q->whereDate('payment_date', $queryDate),
            ])
            ->get();

        $results = [];

        foreach ($cashiers as $cashier) {

            $collegeTotal = $cashier->collegePayment->sum('amount');
            $collegeCount = $cashier->collegePayment->count();

            $shsTotal = $cashier->shsPayment->sum('amount');
            $shsCount = $cashier->shsPayment->count();

            $otherTotal = $cashier->otherPayment->sum('amount');
            $otherCount = $cashier->otherPayment->count();

            $uniformTotal = $cashier->uniformPayment->sum('amount');
            $uniformCount = $cashier->uniformPayment->count();

            $oldAccTotal = $cashier->oldAccountPayment->sum('amount');
            $oldAccCount = $cashier->oldAccountPayment->count();


            $combinedTotal = $collegeTotal + $shsTotal + $otherTotal + $uniformTotal + $oldAccTotal;
            $combinedCount = $collegeCount + $shsCount + $otherCount + $uniformCount + $oldAccCount;

            $collection = CashierCollection::where('cashier_id', $cashier->id)
                ->whereDate('created_at', Carbon::today())
                ->first();

            if (!$collection) {
                $collection = new CashierCollection([
                    'cashier_id' => $cashier->id,
                    'actual_collection' => 0.00,
                    'variance' => 0.00,
                    'note' => '',
                ]);
            }

            $collection->system_collection = $combinedTotal;
            $collection->save();

            if ($combinedTotal > 0) {
                $results[] = [
                    'cashier' => $cashier->name,
                    'collection' => $collection,
                    // 'cashierData' => $cashier
                ];
            }
        }

        return response()->json(['data' => $results]);
    }

    public function printCollection($id)
    {
        $collection = CashierCollection::with(['cashier'])
            ->where('id', $id)
            ->firstOrFail();

        $cashier = $collection->cashier;

        $date = $collection->created_at->toDateString();

        $collegePayments = $cashier->collegePayment()
            ->where(function ($query) {
                $query->where('payment_method', 'CASH')
                    ->orWhereNull('payment_method');
            })
            ->whereDate('payment_date', $date)
            ->get();

        $shsPayments = $cashier->shsPayment()
            ->where(function ($query) {
                $query->where('payment_method', 'CASH')
                    ->orWhereNull('payment_method');
            })
            ->whereDate('payment_date', $date)
            ->get();

        $otherPayments = $cashier->otherPayment()->whereDate('payment_date', $date)->get();
        $uniformPayments = $cashier->uniformPayment()->whereDate('payment_date', $date)->get();
        $oldPayments = $cashier->oldAccountPayment()->whereDate('payment_date', $date)->get();

        $voided = collect()
            ->merge($collegePayments)->merge($shsPayments)
            ->merge($otherPayments)->merge($uniformPayments)
            ->merge($oldPayments)
            ->filter(fn($p) => $p->is_void);

        return view('accountant.collection-summary-print', compact(
            'collection',
            'collegePayments',
            'shsPayments',
            'otherPayments',
            'uniformPayments',
            'oldPayments',
            'voided',
            'cashier'
        ));
    }

    public function updateCollection(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:cashier_collections,id',
            'actual' => 'required|numeric',
            'note' => 'nullable|string'
        ]);

        $collection = CashierCollection::findOrFail($request->id);

        $actual = floatval($request->actual);
        $system = floatval($collection->system_collection);
        $variance = $actual - $system;

        $collection->actual_collection = $actual;
        $collection->variance = $variance;
        $collection->note = $request->note;
        $collection->save();

        return response()->json(['message' => 'Updated successfully.']);
    }

    public function getBillingByStudent($studentId)
    {
        $activeSchoolYear = SchoolYear::where('is_active', 1)->first();

        if (!$activeSchoolYear) {
            return response()->json(['error' => 'No active school year found'], 404);
        }

        $billingData = Billing::where('student_id', $studentId)
            ->where('school_year', $activeSchoolYear->name)
            ->where('semester', $activeSchoolYear->semester)
            ->get();

        return response()->json($billingData);
    }

    /**
     * Show the Statement of Account (SOA) page.
     */
    public function soa()
    {
        // Fetch admissions and scholarships
        $admissions = Admission::with(['programCourseMapping.program'])
            ->orderBy('created_at')
            ->get();
        $scholarships = Scholarship::all();

        // Get active school year
        $activeSchoolYear = SchoolYear::where('is_active', 1)->first();

        if (!$activeSchoolYear) {
            // Handle no active school year case
            $billingData = collect(); // empty collection
            $totalUnits = 0;
            $totalTuitionFees = 0;
            $totalEnrolled = 0;
            $totalFullyPaid = 0;

            // Initialize assessment and payments values
            $assessmentTotal = 0;
            $initialPayment = 0;
            $balanceTotal = 0;
            $dividedBy4 = 0;
            $prelim = 0;
            $midterm = 0;
            $preFinal = 0;
            $final = 0;
        } else {
            // Fetch billing data for active school year and semester
            $billingData = Billing::where('school_year', $activeSchoolYear->name)
                ->where('semester', $activeSchoolYear->semester)
                ->get();

            // Fetch student courses for active school year and semester
            $studentCourses = \App\Models\StudentCourse::where('school_year', $activeSchoolYear->name)
                ->where('semester', $activeSchoolYear->semester)
                ->get();

            // Sum up the units from related courses
            $totalUnits = $studentCourses->sum(function ($studentCourse) {
                return $studentCourse->course->units ?? 0;
            });

            // Fetch total tuition fees from payments for active school year and semester
            $totalTuitionFees = Payment::where('school_year', $activeSchoolYear->name)
                ->where('semester', $activeSchoolYear->semester)
                ->sum('amount');

            // Fetch total number of enrolled students
            $totalEnrolled = Enrollment::where('school_year', $activeSchoolYear->name)
                ->where('semester', $activeSchoolYear->semester)
                ->where('status', '!=', 'Pending') // exclude Pending
                ->count();

            // Fetch total number of fully paid students
            $totalFullyPaid = Billing::where('school_year', $activeSchoolYear->name)
                ->where('semester', $activeSchoolYear->semester)
                ->where('is_full_payment', 1)
                ->count();

            // ASSESSMENT OF FEES
            $assessmentTotal = Billing::where('school_year', $activeSchoolYear->name)
                ->where('semester', $activeSchoolYear->semester)
                ->sum('total_assessment');

            // INITIAL PAYMENT
            $initialPayment = Payment::where('school_year', $activeSchoolYear->name)
                ->where('semester', $activeSchoolYear->semester)
                ->where('grading_period', 'Initial Payment')
                ->sum('amount');

            // BALANCE
            $balanceTotal = Billing::where('school_year', $activeSchoolYear->name)
                ->where('semester', $activeSchoolYear->semester)
                ->sum('balance_due');

            // DIVIDED BY 4 EXAMS
            $dividedBy4 = $assessmentTotal / 4;

            // LESS PAYMENTS: Prelim, Midterm, Pre-Final, Final
            $prelim = Payment::where('school_year', $activeSchoolYear->name)
                ->where('semester', $activeSchoolYear->semester)
                ->where('grading_period', 'Prelims')
                ->sum('amount');

            $midterm = Payment::where('school_year', $activeSchoolYear->name)
                ->where('semester', $activeSchoolYear->semester)
                ->where('grading_period', 'Midterms')
                ->sum('amount');

            $preFinal = Payment::where('school_year', $activeSchoolYear->name)
                ->where('semester', $activeSchoolYear->semester)
                ->where('grading_period', 'prefinals')
                ->sum('amount');

            $final = Payment::where('school_year', $activeSchoolYear->name)
                ->where('semester', $activeSchoolYear->semester)
                ->where('grading_period', 'Finals')
                ->sum('amount');
        }

        // Pass all data to the view
        return view('accountant.soa', compact(
            'admissions',
            'scholarships',
            'billingData',
            'activeSchoolYear',
            'totalUnits',
            'totalTuitionFees',
            'totalEnrolled',
            'totalFullyPaid',
            'assessmentTotal',
            'initialPayment',
            'balanceTotal',
            'dividedBy4',
            'prelim',
            'midterm',
            'preFinal',
            'final'
        ));
    }



    /**
     * Show the student ledger page.
     */
    public function studentLedger()
    {
        $admissions = Admission::with(['programCourseMapping.program'])->orderBy('created_at')->get();
        $scholarships = Scholarship::all();

        // Get active school year
        $activeSchoolYear = SchoolYear::where('is_active', 1)->first();

        if (!$activeSchoolYear) {
            $billingData = collect(); // empty collection
        } else {
            $billingData = Billing::where('school_year', $activeSchoolYear->name)
                ->where('semester', $activeSchoolYear->semester)
                ->get()
                ->keyBy('student_id'); // Map billing data by student_id for quick access
        }

        return view('accountant.student-ledger', compact('admissions', 'scholarships', 'billingData', 'activeSchoolYear'));
    }
    public function ledger(Request $request)
    {
        $studentId = $request->query('student_id');

        $student = Admission::with(['programCourseMapping.program', 'scholarship'])
            ->where('student_id', $studentId)
            ->first();

        if (!$student) {
            return redirect()->back()->withErrors(['Student not found.']);
        }

        $billings = Billing::where('student_id', $studentId)->get();

        $activeSchoolYear = SchoolYear::where('is_active', 1)->first();

        $yearLevelName = 'Unknown Year Level';

        if ($activeSchoolYear) {
            $enrollment = Enrollment::where('student_id', $studentId)
                ->where('school_year', $activeSchoolYear->name)
                ->where('semester', $activeSchoolYear->semester)
                ->first();

            if ($enrollment) {
                $programCourseMapping = ProgramCourseMapping::find($enrollment->course_mapping_id);
                if ($programCourseMapping) {
                    $yearLevel = YearLevel::find($programCourseMapping->year_level_id);
                    if ($yearLevel) {
                        $yearLevelName = $yearLevel->name;
                    }
                }
            }
        }

        return view('accountant.ledger', [
            'student' => $student,
            'billings' => $billings,
            'active_school_year' => $activeSchoolYear->name ?? 'Unknown School Year',
            'active_semester' => $activeSchoolYear->semester ?? 'Unknown Semester',
            'year_level_name' => $yearLevelName,

            // Pass the schedule of payment dates or null if no active sy
            'schedule' => $activeSchoolYear ? [
                'prelims' => $activeSchoolYear->prelims_date,
                'midterms' => $activeSchoolYear->midterms_date,
                'pre_finals' => $activeSchoolYear->pre_finals_date,
                'finals' => $activeSchoolYear->finals_date,
            ] : null,
        ]);
    }
    public function shsSummary()
    {
        try {
            $schoolYears = SchoolYear::all();
            $strands = Strand::all();
            return view('accountant.shs-summary', compact(
                'schoolYears',
                'strands'
            ));
        } catch (\Exception $e) {
            Log::error('Student Summary Error: ' . $e->getMessage());
            $schoolYears = SchoolYear::all();
            $strands = Strand::all();
            return view('accountant.shs-summary', compact(
                'schoolYears',
                'strands'
            ))->with('error', 'Something went wrong.');
        }
    }
    public function getShsSummary($schoolYearId)
    {
        try {
            $summaryData = [];
            $summaryTotals = [

                'initial_payment' => 0,
                'balance' => 0,

                'total_tuition_fees' => 0,
                'total_enrolled' => 0,
                'total_fully_paid' => 0,
                'assessment_total' => 0,
                'balance_total' => 0,
                'total_less' => 0,

                'esc_total' => 0,
                'voucher_total' => 0,
                'novoucher_total' => 0,

                'esc_count' => 0,
                'voucher_count' => 0,
                'novoucher_count' => 0,

            ];

            $schoolYears = $schoolYearId == 0
                ? SchoolYear::all()
                : collect([SchoolYear::findOrFail($schoolYearId)]);

            foreach ($schoolYears as $activeSchoolYear) {
                $admissions = Student::with('enrollment.strand', 'enrollment.payments', 'billing', 'enrollment')->get();

                foreach ($admissions as $student) {
                    $enrollment = $student->enrollment;
                    $billing = $student->billing;
                    if (!$enrollment || !$billing) continue;


                    $initialPayment = $billing->initial_payment ?? 0;
                    $balance = $billing->balance_due ?? 0;


                    $payments = $enrollment->payments()->get();
                    // ->where('school_year', $activeSchoolYear->name)
                    // ->where('semester', $activeSchoolYear->semester);
                    $paymentsArray = [];
                    $total = 0;
                    $paymentCount = 1;
                    foreach ($payments as $p) {
                        if ($p->amount > 0) {
                            $paymentsArray[] = $p->amount ?? '';
                            $total += $p->amount;
                            if ($paymentCount === 5) {
                                break;
                            }
                            $paymentCount++;
                        }
                    }
                    $remainingBalance = $balance - $total;
                    $summaryData[] = [
                        'yearSemester' => $billing->semester . ' ' . $activeSchoolYear->name,
                        'student_no' => $student->student_id,
                        'name' => $student->full_name,
                        'initial_payment' => $initialPayment,
                        'balance' => $balance,
                        'less_payments' => $paymentsArray,
                        'rem_balance' => $remainingBalance,
                        'lrn_number' => $student->lrn_number,
                        'strand' => $enrollment->strand,
                        'type_of_pee' => $enrollment->type_of_payee,
                    ];
                }

                $esc = ShsEnrollment::where('school_year', $activeSchoolYear->name)
                    ->where('type_of_payee', 'With ESC')
                    ->with('student.billing')
                    ->get();

                $withv = ShsEnrollment::where('school_year', $activeSchoolYear->name)
                    ->where('type_of_payee', 'With Voucher')
                    ->with('student.billing')
                    ->get();

                $nov = ShsEnrollment::where('school_year', $activeSchoolYear->name)
                    ->where('type_of_payee', 'No Voucher')
                    ->with('student.billing')
                    ->get();

                $summaryTotals['novoucher_total'] += $nov->sum(function ($item) {
                    return optional(optional($item->student)->billing)->balance_due ?? 0;
                });

                $summaryTotals['voucher_total'] += $withv->sum(function ($item) {
                    return optional(optional($item->student)->billing)->balance_due ?? 0;
                });

                $summaryTotals['esc_total'] += $esc->sum(function ($item) {
                    return optional(optional($item->student)->billing)->balance_due ?? 0;
                });


                $summaryTotals['novoucher_count'] += $nov->count();
                $summaryTotals['voucher_count'] += $withv->count();
                $summaryTotals['esc_count'] += $esc->count();


                $summaryTotals['total_less'] += ShsPayment::where('school_year', $activeSchoolYear->name)
                    ->sum('amount');

                $summaryTotals['total_tuition_fees'] += ShsPayment::where('school_year', $activeSchoolYear->name)
                    ->sum('amount');

                $summaryTotals['total_enrolled'] += ShsEnrollment::where('school_year', $activeSchoolYear->name)
                    ->where('status', '!=', 'Pending')
                    ->count();

                $summaryTotals['total_fully_paid'] += ShsBilling::where('school_year', $activeSchoolYear->name)
                    ->where('is_full_payment', 1)
                    ->count();

                $assessment = ShsBilling::where('school_year', $activeSchoolYear->name)
                    ->sum('total_assessment');
                $summaryTotals['assessment_total'] += $assessment;

                $summaryTotals['initial_payment'] += ShsBilling::where('school_year', $activeSchoolYear->name)
                    ->sum('initial_payment');

                $summaryTotals['balance_total'] += ShsBilling::where('school_year', $activeSchoolYear->name)
                    ->sum('balance_due');
            }

            return response()->json([
                'students' => $summaryData,
                'summary' => $summaryTotals
            ]);
        } catch (\Exception $e) {
            Log::error('Student Summary Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Something went wrong.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function getStudentSummary($schoolYearId)
    {
        try {
            $summaryData = [];
            $summaryTotals = [
                'total_units' => 0,
                'total_tuition_fees' => 0,
                'total_enrolled' => 0,
                'total_fully_paid' => 0,
                'assessment_total' => 0,
                'initial_payment' => 0,
                'balance_total' => 0,
                'divided_by_4' => 0,
                'prelim' => 0,
                'midterm' => 0,
                'prefinal' => 0,
                'final' => 0,
            ];

            $schoolYears = $schoolYearId == 0
                ? SchoolYear::all()
                : collect([SchoolYear::findOrFail($schoolYearId)]);

            foreach ($schoolYears as $activeSchoolYear) {
                $admissions = Admission::with('courseMapping.course', 'courseMapping.yearLevel', 'payments', 'scholarship', 'billing', 'enrollment')->get();

                foreach ($admissions as $student) {
                    $courseMapping = $student->courseMapping;
                    $billing = $student->billing;
                    if (!$courseMapping || !$billing) continue;

                    $semester = Semester::where('name', $activeSchoolYear->semester)->first();

                    $mapping = ProgramCourseMapping::where('program_id', $courseMapping->program->id ?? 0)
                        ->where('year_level_id', $courseMapping->yearLevel->id ?? 0)
                        ->where('semester_id', $semester->id ?? 0)
                        ->with('course')->get();

                    $unitsCount = $mapping->sum(fn($m) => $m->course->units ?? 0);
                    $tuitionPerUnit = $activeSchoolYear->default_unit_price;

                    if (optional($courseMapping->yearLevel)->id === 4 && $activeSchoolYear->name === '2025-2026') {
                        $tuitionPerUnit = 504;
                    }

                    $totalTuition = $unitsCount * $tuitionPerUnit;
                    $discount = $billing->discount ?? 0;
                    $netTuition = $totalTuition - $discount;
                    $miscFees = $billing->misc_fee ?? 0;
                    $oldBalance = $billing->old_accounts ?? 0;
                    $totalPayable = $netTuition + $miscFees + $oldBalance;
                    $initialPayment = $billing->initial_payment ?? 0;
                    $balance = $billing->balance_due ?? 0;
                    $perExam = $balance / 4;

                    $payments = $student->payments()
                        ->where('school_year', $activeSchoolYear->name)
                        ->where('semester', $activeSchoolYear->semester);

                    $prelim = (clone $payments)->where('grading_period', 'Prelims')->sum('amount');
                    $midterm = (clone $payments)->where('grading_period', 'Midterms')->sum('amount');
                    $prefinal = (clone $payments)->where('grading_period', 'Prefinals')->sum('amount');
                    $final = (clone $payments)->where('grading_period', 'Finals')->sum('amount');

                    $totalPaid = (clone $payments)->where('is_void', false)->sum('amount');
                    $remainingBalance = $totalPayable - $totalPaid;

                    $discountPercentage = $totalTuition > 0 ? ($discount / $totalTuition) * 100 : 0;

                    $summaryData[] = [
                        'yearSemester' => $billing->semester . ' ' . $activeSchoolYear->name,
                        'student_no' => $student->student_id,
                        'name' => $student->full_name,
                        'yearCourse' => (optional($courseMapping->yearLevel)->name ?? 'N/A') . ' – ' . (optional($courseMapping->program)->code ?? 'N/A'),
                        'yearLevel' => optional($courseMapping->yearLevel)->name ?? 'N/A',
                        'program' => optional($courseMapping->program)->code ?? 'N/A',
                        'units' => $unitsCount,
                        'fee_per_unit' => $tuitionPerUnit,
                        'total_tuition' => $totalTuition,
                        'discount' => ceil($discountPercentage),
                        'net_tuition' => $netTuition,
                        'misc' => $miscFees,
                        'old_balance' => $oldBalance,
                        'total_payable' => $totalPayable,
                        'initial_payment' => $initialPayment,
                        'balance' => $balance,
                        'per_exam' => $perExam,
                        'prelims' => $prelim,
                        'midterm' => $midterm,
                        'prefinal' => $prefinal,
                        'finals' => $final,
                        'total_paid' => $totalPaid,
                        'total_remaining' => $remainingBalance,
                        'scholarship' => optional($student->scholarship)->name ?? 'None',
                    ];
                }

                //  Summary for each school year
                $studentCourses = StudentCourse::where('school_year', $activeSchoolYear->name)
                    ->where('semester', $activeSchoolYear->semester)
                    ->get();
                $summaryTotals['total_units'] += $studentCourses->sum(fn($sc) => $sc->course->units ?? 0);

                $summaryTotals['total_tuition_fees'] += Payment::where('school_year', $activeSchoolYear->name)
                    ->where('semester', $activeSchoolYear->semester)
                    ->sum('amount');

                $summaryTotals['total_enrolled'] += Enrollment::where('school_year', $activeSchoolYear->name)
                    ->where('semester', $activeSchoolYear->semester)
                    ->where('status', '!=', 'Pending')
                    ->count();

                $summaryTotals['total_fully_paid'] += Billing::where('school_year', $activeSchoolYear->name)
                    ->where('semester', $activeSchoolYear->semester)
                    ->where('is_full_payment', 1)
                    ->count();

                $assessment = Billing::where('school_year', $activeSchoolYear->name)
                    ->where('semester', $activeSchoolYear->semester)
                    ->sum('total_assessment');
                $summaryTotals['assessment_total'] += $assessment;

                $summaryTotals['initial_payment'] += Payment::where('school_year', $activeSchoolYear->name)
                    ->where('semester', $activeSchoolYear->semester)
                    ->where('grading_period', 'Initial Payment')
                    ->sum('amount');

                $summaryTotals['balance_total'] += Billing::where('school_year', $activeSchoolYear->name)
                    ->where('semester', $activeSchoolYear->semester)
                    ->sum('balance_due');

                $summaryTotals['divided_by_4'] += $assessment / 4;

                $summaryTotals['prelim'] += Payment::where('school_year', $activeSchoolYear->name)
                    ->where('semester', $activeSchoolYear->semester)
                    ->where('grading_period', 'Prelims')
                    ->sum('amount');

                $summaryTotals['midterm'] += Payment::where('school_year', $activeSchoolYear->name)
                    ->where('semester', $activeSchoolYear->semester)
                    ->where('grading_period', 'Midterms')
                    ->sum('amount');

                $summaryTotals['prefinal'] += Payment::where('school_year', $activeSchoolYear->name)
                    ->where('semester', $activeSchoolYear->semester)
                    ->where('grading_period', 'Prefinals')
                    ->sum('amount');

                $summaryTotals['final'] += Payment::where('school_year', $activeSchoolYear->name)
                    ->where('semester', $activeSchoolYear->semester)
                    ->where('grading_period', 'Finals')
                    ->sum('amount');
            }

            return response()->json([
                'students' => $summaryData,
                'summary' => $summaryTotals
            ]);
        } catch (\Exception $e) {
            Log::error('Student Summary Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Something went wrong.',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function studentSummary()
    {
        try {
            $schoolYears = SchoolYear::all();
            $programs = Program::all();
            return view('accountant.student-summary', compact(
                'schoolYears',
                'programs'
            ));
        } catch (\Exception $e) {
            Log::error('Student Summary Error: ' . $e->getMessage());
            $schoolYears = SchoolYear::all();
            $programs = Program::all();
            return view('accountant.student-summary', compact(
                'schoolYears',
                'programs'
            ))->with('error', 'Something went wrong.');
        }
    }


    public function getOldAccounts()
    {
        try {
            $oldAccounts = OldAccount::all();
            return view('accountant.old-accounts', compact('oldAccounts'));
        } catch (\Exception $e) {
            return view('accountant.old-accounts')->with('error', 'Something went wrong.');
        }
    }
    public function pendingVoids()
    {
        // {   
        $collegePayments = Payment::with(['student', 'student.enrollment.programCourseMapping.program'])
            ->where('status', 'pending_void')
            ->orderBy('payment_date', 'desc')
            ->get();

        $shsPayments = ShsPayment::with(['student', 'enrollment.strand'])
            ->where('status', 'pending_void')
            ->orderBy('payment_date', 'desc')
            ->get();

        $payments = $collegePayments->merge($shsPayments);

        return view('accountant.pending-voids', compact('payments'));
    }

    public function approveVoid(Payment $payment)
    {
        DB::transaction(function () use ($payment) {
            // Check payment_type first
            if ($payment->payment_type === 'others') {
                // If payment_type is "others", only update status
                $payment->status = 'void_approved';
                $payment->save();
                return; // Exit the transaction early
            }

            // Proceed with the normal void approval logic
            $user = Auth::user();
            $payment->status = 'void_approved';
            $payment->is_void = true;
            $payment->voided_at = Carbon::now();
            // $payment->voided_by = $user->id;
            $payment->save();


            // Get billing record for this student + school year + semester
            $billing = Billing::where('student_id', $payment->student_id)
                ->where('school_year', $payment->school_year)
                ->where('semester', $payment->semester)
                ->first();

            if (!$billing) {
                throw new \Exception('Billing record not found.');
            }

            // The grading period for the voided payment, e.g. 'prelims'
            $gradingPeriod = $payment->grading_period;

            // Validate the grading period matches a due column
            $validPeriods = ['prelims', 'midterms', 'prefinals', 'finals'];
            if (!in_array($gradingPeriod, $validPeriods)) {
                throw new \Exception("Invalid grading period '{$gradingPeriod}' in payment.");
            }

            // Column to add amount back to
            $dueColumn = $gradingPeriod . '_due';

            // Add the voided amount back to the due column
            $billing->$dueColumn += $payment->amount;

            // Recalculate balance_due as sum of all dues
            $billing->balance_due =
                $billing->prelims_due +
                $billing->midterms_due +
                $billing->prefinals_due +
                $billing->finals_due;

            // Update is_full_payment flag
            $billing->is_full_payment = $billing->balance_due <= 0;

            $billing->save();
        });

        return redirect()->route('accountant.pending_voids')->with('success', 'Void approved and billing updated.');
    }

    public function approveShsVoid(ShsPayment $payment)
    {
        try {
            DB::transaction(function () use ($payment) {
                // Proceed with the normal void approval logic
                $payment->status = 'void_approved';
                $payment->is_void = true;
                $payment->save();

                $student = $payment->enrollment->student;

                // Get billing record for this student + school year + semester
                $billing = ShsBilling::where('student_lrn', $student->lrn_number)
                    ->where('school_year', $payment->school_year)
                    ->first();

                if (!$billing) {
                    throw new \Exception('Billing record not found.');
                }
                // Recalculate balance_due as sum of all dues
                $billing->balance_due = $payment->amount + $billing->balance_due;
                // Update is_full_payment flag
                $billing->is_full_payment = $billing->balance_due <= 0;

                $billing->save();
            });

            return redirect()->route('accountant.pending_voids')->with('success', 'Void approved and billing updated.');
        } catch (\Exception $e) {
            return redirect()->route('accountant.pending_voids')->with('error', $e->getMessage());
        }
    }
    /**
     * Reject void request.
     */
    public function rejectVoid(Payment $payment)
    {
        $payment->status = 'void_rejected';
        $payment->is_void = false;
        $payment->save();

        return redirect()->route('accountant.pending_voids')->with('success', 'Void request rejected.');
    }
    public function rejectShsVoid(ShsPayment $payment)
    {
        $payment->status = 'void_rejected';
        $payment->is_void = false;
        $payment->save();

        return redirect()->route('accountant.pending_voids')->with('success', 'Void request rejected.');
    }
    /**
     * Show the promisories page.
     */
    public function promisories()
    {
        // Fetch any necessary data for promisories here.
        return view('promisories');
    }
}
