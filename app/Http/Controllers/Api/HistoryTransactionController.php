<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\HistoryRequestResource;
use App\Http\Resources\HistoryTransactionResource;

class HistoryTransactionController extends Controller
{
    public function showByUser(Request $request)
    {
        try {
            $user = Auth::user();
            $status = $request->input('status'); // Ambil filter status dari request
            $search = $request->input('search'); // Ambil kata kunci pencarian dari request


            $transactions = Transaction::whereHas('request', function ($query) use ($user, $search) {
                $query->where('id_user', $user->id);
                // Pencarian berdasarkan nama mua
                if ($search) {
                    $query->whereHas('mua', function ($muaQuery) use ($search) {
                        $muaQuery->where('name', 'like', '%' . $search . '%');
                    });
                }
            })
                ->when($status, function ($query) use ($status) {
                    return $query->where('status', $status);
                })
                ->with(['request.mua', 'request.requestPackages'])
                ->get();

            if ($transactions->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No transaction found',
                    'data' => []
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => $transactions->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'mua' => [
                            'id' => $transaction->request->mua->id,
                            'name' => $transaction->request->mua->name,
                        ],
                        'packages' => $transaction->request->requestPackages->map(function ($requestPackage) {
                            $packageDetails = json_decode($requestPackage->package_details);
                            return [
                                'id' => $packageDetails->id,
                                'package_name' => $packageDetails->package_name,
                                'price' => $packageDetails->price,
                                'image' => $packageDetails->image ? url('storage/images/packages/' . $packageDetails->image) : null,
                                'quantity' => $requestPackage->quantity,
                                'total_per_package' => $packageDetails->total_per_package,
                            ];
                        }),
                        'date' => $transaction->request->date->format('Y-m-d'),
                        'status' => $transaction->status,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }




    public function show($id)
    {
        try {
            $transactions = Transaction::where('id', $id)
                ->with(['request.mua', 'request.requestPackages']) // Eager load request, mua, dan packages
                ->get();

            if ($transactions->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No transaction found'
                ]);
            }
            return response()->json([
                'status' => 'success',
                'data' => HistoryTransactionResource::collection($transactions)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function showByMua(Request $request)
    {
        try {
            $mua = Auth::user();

            $status = $request->input('status'); // Ambil filter status dari request
            $search = $request->input('search'); // Ambil kata kunci pencarian dari request

            // Query transaksi terkait dengan user
            $transactions = Transaction::whereHas('request', function ($query) use ($mua, $status, $search) {
                $query->where('id_mua', $mua->id);

                // Pencarian berdasarkan nama User
                if ($search) {
                    $query->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%');
                    });
                }
            })
                ->when($status, function ($query) use ($status) {
                    return $query->where('status', $status);
                })
                ->with(['request.user', 'request.requestPackages']) // Eager load request, user, dan packages
                ->get();

            if ($transactions->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No transaction found',
                    'data' => []
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => $transactions->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'user' => [
                            'id' => $transaction->request->user->id,
                            'name' => $transaction->request->user->name,
                        ],
                        'packages' => $transaction->request->requestPackages->map(function ($requestPackage) {
                            $packageDetails = json_decode($requestPackage->package_details);
                            return [
                                'id' => $packageDetails->id,
                                'package_name' => $packageDetails->package_name,
                                'price' => $packageDetails->price,
                                'image' => $packageDetails->image ? url('storage/images/packages/' . $packageDetails->image) : null,
                                'quantity' => $requestPackage->quantity,
                                'total_per_package' => $packageDetails->total_per_package,
                            ];
                        }),
                        'date' => $transaction->request->date->format('Y-m-d'),
                        'status' => $transaction->status,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
