<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\RequestResource;

class TransactionController extends Controller
{
    public function showUserTransactions()
    {
        $userId = Auth::id();

        $transactions = Transaction::whereHas('request', function ($query) use ($userId) {
            $query->where('id_user', $userId);
        })
        ->with('request') 
        ->get();

        $muaId = $transactions->pluck('request.id_mua')->unique();

        $paymentMethodTypes = PaymentMethod::whereIn('mua_id', $muaId)
            ->where('status', 'active')
            ->with('type')
            ->get()
            ->pluck('type.type') 
            ->unique(); 

        return response()->json([
            'transactions' => $transactions,
            'payment_method_types' => $paymentMethodTypes 
        ], 200);
    }


    public function showTransactionsByMUA()
    {
        $muaId = Auth::id();

        $transactions = Transaction::whereHas('request', function($query) use ($muaId) {
            $query->where('id_mua', $muaId);
        })->with('request')->get();

        return response()->json([
            'transactions' => $transactions
        ], 200);
    }

    public function uploadPaymentProof(Request $request, $transactionId)
    {

        $transaction = Transaction::findOrFail($transactionId);

        if (Auth::id() !== $transaction->request->id_user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');

            $filename = $transaction->created_at->timestamp . '_' . $transaction->id . '.' . $file->getClientOriginalExtension();

            $file->storeAs('public/images', $filename);

            $transaction->update([
                'payment_proof' => $filename,
                'payment_status' => 'unpaid', 
            ]);

            return response()->json(['message' => 'Payment proof uploaded successfully.'], 200);
        }

        return response()->json(['message' => 'Failed to upload payment proof.'], 400);
    }

    public function confirmPayment($transactionId)
{
    $transaction = Transaction::findOrFail($transactionId);

    if (Auth::id() !== $transaction->request->id_mua) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    if (is_null($transaction->payment_proof)) {
        return response()->json(['message' => 'No payment proof found.'], 400);
    }

    $transaction->update(['payment_status' => 'paid off']);

    return response()->json(['message' => 'Payment confirmed successfully.'], 200);
}

    public function requestCancel($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);

            if (Auth::id() !== $transaction->request->id_user) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $transactionCreatedAt = $transaction->updated_at;
            $threeHoursLater = $transactionCreatedAt->copy()->addHours(3);

            if (now()->greaterThan($threeHoursLater)) {
                return response()->json(['message' => 'Request to cancel the transaction can only be made within 3 hours of the transaction.'], 403);
            }

            if ($transaction->status === 'unpaid') {
                $transaction->update(['status' => 'canceled']);
                $transaction->request->update(['status' => 'canceled']);
                return response()->json(['message' => 'Transaction successfully cancelled.'], 200);
            } else {
                $transaction->update(['status' => 'request cancel']);
                return response()->json(['message' => 'Request to cancel the transaction has been sent.'], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function showCancelRequest(){
        try {
            $transactions = Transaction::where('status', 'request cancel')
                ->with('request')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $transactions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function approveCancel($id)
    {
        try {
            $transaction = Transaction::with('request')->findOrFail($id);

            if (Auth::id() !== $transaction->request->id_mua) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $transaction->update(['status' => 'canceled']);
            $transaction->request->update(['status' => 'canceled']);

            return response()->json(['message' => 'Transaction successfully cancelled.'], 200);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to cancel the request.'], 400);
        }
    }

    public function rejectCancel($id)
    {
        try {
            $transaction = Transaction::with('request')->findOrFail($id);

            if (Auth::id() !== $transaction->request->id_mua) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            // Update `status` to match `payment_status` when the cancel request is rejected
            $transaction->update(['status' => $transaction->payment_status]);

            return response()->json(['message' => 'Request has been rejected.'], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reject the request.'
            ], 400);
        }
    }


}
