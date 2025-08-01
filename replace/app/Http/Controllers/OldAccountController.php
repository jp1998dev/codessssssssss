<?php

namespace App\Http\Controllers;

use App\Models\OldAccount;
use Illuminate\Http\Request;

class OldAccountController extends Controller
{
    public function markAsPaid($id)
{
    try {
        $account = OldAccount::findOrFail($id);
        $account->is_paid = true;
        $account->save();

        return response()->json([
            'message' => 'Old account marked as paid.',
            'data' => $account
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to mark as paid.',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function store(Request $request)
    {
        try {
           
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'course_strand' => 'required|string|max:255',
                'year_graduated' => 'required|string|max:255',
                'balance' => 'required|numeric|min:0',
                'particular' => 'required|string|max:255',
                'remarks' => 'nullable|string|max:255',
            ]);

            $oldAccount = OldAccount::create($validated);

            return response()->json([
                'message' => 'Old account record created successfully.',
                'data' => $oldAccount
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
