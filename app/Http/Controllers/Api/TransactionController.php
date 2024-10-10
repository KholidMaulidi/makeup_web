<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;

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

}
