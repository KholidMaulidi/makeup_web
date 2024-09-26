<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Request as UserRequest;
use App\Models\HistoryRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\HistoryRequestResource;
use Carbon\Carbon;

class HistoryRequestController extends Controller
{
    public function userHistory(Request $request)
    {
        $userId = Auth::id(); // Mengambil ID user yang sedang login
        
        // Ambil history request yang berkaitan dengan user
        $history = HistoryRequest::where('id_user', $userId)
                                ->where('status', 'completed')
                                ->paginate(10);

        // Jika history kosong, kembalikan respons dengan pesan
        if ($history->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No completed requests found in history.',
            ], 200);
        }

        // Kembalikan history yang ditemukan
        return HistoryRequestResource::collection($history);
    }

    public function muaHistory(Request $request)
    {
        $muaId = Auth::id(); // Mengambil ID MUA yang sedang login
        
        // Ambil history request yang berkaitan dengan MUA
        $history = HistoryRequest::where('id_mua', $muaId)
                                ->where('status', 'completed')
                                ->paginate(10);

        // Jika history kosong, kembalikan respons dengan pesan
        if ($history->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No completed requests found in history.',
            ], 200);
        }

        // Kembalikan history yang ditemukan
        return HistoryRequestResource::collection($history);
    }

}
