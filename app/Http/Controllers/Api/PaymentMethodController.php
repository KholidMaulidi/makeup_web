<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Traits\JsonResponseTrait;

class PaymentMethodController extends Controller
{
    use JsonResponseTrait;

    public function getAllPaymentMethods()
    {
        $paymentMethods = PaymentMethod::where('mua_id', Auth::id())->get();

        if ($paymentMethods->isEmpty()) {
            return response()->json(['message' => 'No payment methods found'], 404);
        }

        return response()->json($paymentMethods);
    }

    public function createPaymentMethod(Request $request)
    {
        try {
            $data = $request->validate([
                'type_id' => 'required|exists:payment_method_types,id',
                'payment_method_name' => 'nullable|string',
                'payment_method_number' => 'nullable|string|unique:payment_methods,payment_method_number',
            ]);

            if ($data['type_id'] == 1) {
                $data['payment_method_name'] = null;
                $data['payment_method_number'] = null;
            } else {
                $request->validate([
                    'payment_method_name' => 'required|string',
                    'payment_method_number' => 'required|string|unique:payment_methods,payment_method_number',
                ]);
            }

            $data['mua_id'] = Auth::id();
            $payment = PaymentMethod::create($data);

            return $this->successResponse($payment, 'Payment method created successfully', 201);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }

    public function updatePaymentMethod(Request $request, $id)
    {
        try {
            $payment = PaymentMethod::where('mua_id', Auth::id())->findOrFail($id);

            $data = $request->validate([
                'type_id' => 'sometimes|exists:payment_method_types,id',
                'payment_method_name' => 'nullable|string',
                'payment_method_number' => 'nullable|string|unique:payment_methods,payment_method_number,' . $id,
            ]);

            if (isset($data['type_id']) && $data['type_id'] == 1) {
                $data['payment_method_name'] = null;
                $data['payment_method_number'] = null;
            } else {
                $request->validate([
                    'payment_method_name' => 'required|string',
                    'payment_method_number' => 'required|string|unique:payment_methods,payment_method_number,' . $id,
                ]);
            }

            $payment->update($data);

            return $this->successResponse($payment, 'Payment method updated successfully', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }

    public function deletePaymentMethod($id)
    {
        try {
            $payment = PaymentMethod::where('mua_id', Auth::id())->findOrFail($id);

            $payment->delete();

            return $this->successResponse([], 'Payment method deleted successfully', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }

    public function updatePaymentMethodStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:active,off',
            ]);

            $paymentMethod = PaymentMethod::where('mua_id', Auth::id())->findOrFail($id);

            $paymentMethod->update([
                'status' => $request->input('status')
            ]);

            return $this->successResponse($paymentMethod, 'Payment method status updated successfully', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), [], 500);
        }
    }

    public function showPaymentMethodsByType($transaction_id, $type_id)
{
    try {
        $userId = Auth::id();

        $transaction = Transaction::where('id', $transaction_id)
            ->whereHas('request', function ($query) use ($userId) {
                $query->where('id_user', $userId);
            })
            ->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found or you do not have access'], 404);
        }

        $muaId = $transaction->request->id_mua;

        $paymentMethods = PaymentMethod::where('mua_id', $muaId)
            ->where('type_id', $type_id)
            ->where('status', 'active')
            ->with('type')
            ->get();

        if ($paymentMethods->isEmpty()) {
            return response()->json(['message' => 'No payment methods found for this type'], 404);
        }

        return response()->json($paymentMethods, 200);
    } catch (\Throwable $th) {
        return $this->errorResponse($th->getMessage(), [], 500);
    }
}
}
