<?php

namespace App\Http\Controllers;

use App\Models\BankDeposit;
use App\Models\OldAccPayment;
use App\Models\OtherPayment;
use App\Models\Payment;
use App\Models\ShsPayment;
use App\Models\UniformPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BankDepositController extends Controller
{
    public function store(Request $request)
    {
        try {
            if (!$request->slip) {
                return response()->json(['message' => 'Slip cannot be null.'], 422);
            }
            if (!$request->total_deposited) {
                return response()->json(['message' => 'Total Deposit cannot be null.'], 422);
            }

            $validated = $request->validate([
                'total_deposited' => 'required|numeric|min:0',
                'slip' => 'required|string|max:255',
                'remarks' => 'required|string|max:255',
            ]);
            $date = Carbon::now();
            $collegePayments = Payment::where('payment_method', 'CASH')
                ->whereDate('payment_date', $date)
                ->sum('amount');

            $shsPayments = ShsPayment::where('payment_method', 'CASH')
                ->whereDate('payment_date', $date)
                ->sum('amount');

            $otherPayments = OtherPayment::whereDate('payment_date', $date)
                ->sum('amount');

            $uniformPayments = UniformPayment::whereDate('payment_date', $date)->sum('amount');
            $oldPayments = OldAccPayment::whereDate('payment_date', $date)->sum('amount');

            $systemTotal = $collegePayments + $shsPayments + $otherPayments + $uniformPayments + $oldPayments;
            if ($systemTotal < $validated['total_deposited']) {
                return response()->json([
                    'message' => 'Total deposited cannot be greater than today\'s system collection (₱' . number_format($systemTotal, 2) . ').'
                ], 422);
            }
            $existing = BankDeposit::where('slip', $validated['slip'])
                ->first();

            if ($existing) {
                return response()->json([
                    'message' => 'Another record already uses this deposit slip number.'
                ], 422);
            }
            BankDeposit::create([
                'system_collection' => $systemTotal,
                'total_deposited' => $validated['total_deposited'],
                'slip' => $validated['slip'],
                'remarks' => $validated['remarks'],
            ]);

            return response()->json(['message' => 'New Deposit Added.']);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            if (!$request->slip) {
                return response()->json(['message' => 'Slip cannot be null.'], 422);
            }
            if (!$request->total_deposited) {
                return response()->json(['message' => 'Total Deposit cannot be null.'], 422);
            }

            $validated = $request->validate([
                'total_deposited' => 'required|numeric|min:0',
                'slip' => 'nullable|string|max:255',
                'remarks' => 'nullable|string|max:255',
            ]);
            $existing = BankDeposit::where('slip', $validated['slip'])
                ->where('id', '!=', $id)
                ->first();

            if ($existing) {
                return response()->json([
                    'message' => 'Another record already uses this deposit slip number.'
                ], 422);
            }

            $deposit = BankDeposit::findOrFail($id);
            $systemTotal = $deposit->system_collection;
            if ($systemTotal < $validated['total_deposited']) {
                return response()->json([
                    'message' => 'Total deposited cannot be greater than system collection (₱' . number_format($systemTotal, 2) . ').'
                ], 422);
            }
            $deposit->update($validated);

            return response()->json(['message' => 'Deposit updated.']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
