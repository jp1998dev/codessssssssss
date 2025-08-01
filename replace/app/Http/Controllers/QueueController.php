<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Billing;
use App\Models\TransanctionWindow;
use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\ShsBilling;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Svg\Tag\Rect;

class QueueController extends Controller
{
    //


    public function saveQueue(Request $request)
    {
        try {

            $date = Carbon::today();

            Queue::whereDate('date_created', '!=', $date)->delete();
            $validatedData = $request->validate([
                'transaction_id' => 'required|integer',
                'name' => 'required|string|max:255',

            ]);
            $transactionId = $validatedData['transaction_id'];
            $name = $validatedData['name'];


            $size = Queue::all()->count();
            $queueNumber = ($size >= 9999) ? 1000 : 1000 + $size;
            $newQuueuNo = str_pad($queueNumber, 4, '0', STR_PAD_LEFT);

            $nextQueue = Queue::create([
                'queue_no' => $newQuueuNo,
                'name' => $name,
                'window_id' => 0,
                'status' => 0,
                'transaction_id' => $transactionId,
                'status_trigger' => '0',
                'purpose' => 'n/a',
            ]);

            return response()->json(['status' => 1, 'data' => $nextQueue]);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'error' => $e->getMessage()]);
        }
    }
    public function updateQueue(Request $request)
    {
        try {
            $user = Auth::user();

            $validatedData = $request->validate([
                'transaction_id' => 'required|integer',
            ]);


            Queue::whereDate('date_created', '!=', now()->toDateString())->delete();

            $transactionId = $validatedData['transaction_id'];


            $window = TransanctionWindow::where('transaction_id', $transactionId)
                ->where('status', 1)
                ->where('user_id', $user->id)
                ->first();

            if (!$window) {
                return response()->json(['status' => 0, 'error' => 'Window not found for this cashier.']);
            }


            $currentQueue = Queue::where('window_id', $window->id)
                ->where('status', 1)
                ->where('status_trigger', 0)
                ->first();

            if ($currentQueue) {
                $currentQueue->status_trigger = '1';
                $currentQueue->save();
            }

            $nextQueue = Queue::where('transaction_id', $transactionId)
                ->where('status', 0)
                ->orderBy('id', 'asc')
                ->first();
            $queues = Queue::all();
            if (!$nextQueue) {
                return response()->json(['status' => 0, 'error' => 'No pending queue.', 'queues' => $queues, 'next' => $nextQueue, "req" => $request->all()]);
            }

            $queues = Queue::all();
            $nextQueue->status = 1;
            $nextQueue->status_trigger = '0';
            $nextQueue->window_id = $window->id;
            $nextQueue->save();

            return response()->json(['status' => 1, 'data' => $nextQueue, 'queues' => $queues]);
        } catch (\Exception $e) {
            $queues = Queue::all();
            return response()->json(['status' => 0, 'error' => $e->getMessage(), 'queues' => $queues]);
        }
    }

    // public function newQueue(Request $request)
    // {
    //     try {



    //     } catch (\Exception $e) {
    //         return response()->json(['status' => 0, 'error' => $e->getMessage()]);
    //     }
    // }
    public function updateRecall($sid, Request $request)
    {
        try {
            $user = Auth::user();


            $window = TransanctionWindow::where('user_id', $user->id)->first();
            if (!$window) {
                return response()->json(['status' => 0, 'error' => 'Cashier window not found.']);
            }


            $queueItem = Queue::where('id', $sid)
                ->where('window_id', $window->id)
                ->first();

            if (!$queueItem) {
                return response()->json(['status' => 0, 'error' => 'Queue not found or not yours.']);
            }

            $queueItem->status_trigger = '0';
            $queueItem->save();

            return response()->json([
                'status' => 1,
                'data' => [
                    'id' => $queueItem->id,
                    'name' => $queueItem->name,
                    'queue_no' => $queueItem->queue_no,
                    'purpose' => $queueItem->purpose,
                    'wname' => optional($queueItem->window)->name,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'error' => $e->getMessage()]);
        }
    }


    public function getLatestQueue(Request $request)
    {
        try {
            Queue::whereDate('date_created', '!=', now()->toDateString())
                ->delete();
            $queueItem = Queue::where('status', 1)
                ->where('status_trigger', '0')
                ->orderBy('id', 'asc')
                ->first();

            if (!$queueItem) {
                return response()->json(['status' => 0, 'error' => 'No queue available.', 'queue' => $queueItem]);
            }
            $queueItem->status_trigger = '1';
            $queueItem->save();
            return response()->json(['status' => 1, 'data' => [
                'name' => $queueItem->name,
                'window_id' => $queueItem->window_id,
                'queue_no' => $queueItem->queue_no,
            ]], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'error' => $e->getMessage()]);
        }
    }

    public function getStudentBalance(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'studentCode' => 'required',
                'is_college' => 'required'
            ]);

            if ($validatedData['is_college']) {
                $student = Admission::where('student_id', $validatedData['studentCode'])->first();
                $billing = Billing::where('student_id', $validatedData['studentCode'])->first();
            } else {
                $student = Student::where('lrn_number', $validatedData['studentCode'])->first();
                $billing = ShsBilling::where('student_lrn', $validatedData['studentCode'])->first();
            }

            if (!$student || !$billing) {
                return response()->json(['status' => 0, 'error' => 'Student or billing not found']);
            }

            $balance = $billing->balance_due;
            $fullName = $student->last_name . ', ' . $student->first_name . ' ' . $student->middle_name;
            $date = now()->format('F d, Y');

            return response()->json([
                'status' => 1,
                'balance' => $balance,
                'name' => $fullName,
                'date' => $date,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'error' => $e->getMessage()]);
        }
    }
}
