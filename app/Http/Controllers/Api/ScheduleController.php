<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Request as UserRequest;
use Illuminate\Http\Request as HttpRequest;
use App\Http\Resources\ScheduleResource;
use App\Http\Resources\ScheduleMuaResource;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ScheduleController extends Controller
{

    // add day liburr
    public function getSchedules()
    {
        $muaId = Auth::id();

        $schedules = UserRequest::where('id_mua', $muaId)
            ->where('status', 'approved')
            ->paginate(3);

        if ($schedules->isEmpty()) {
            return response()->json([
                'message' => 'No schedules available',
            ], 200);
        }

            return response()->json([
                'schedules' => ScheduleResource::collection($schedules)
            ]);
    }

    public function getMuaSchedules(Request $request, $id_mua)
{
    // Validasi parameter `filtereddate` jika diberikan
    $validatedData = $request->validate([
        'filteredDate' => 'nullable|date'
    ]);

    // Cek apakah `filteredDate` diberikan dan valid
    if (isset($validatedData['filteredDate'])) {
        $date = Carbon::parse($validatedData['filteredDate']);
        $schedules = UserRequest::where('id_mua', $id_mua)
            ->where('status', 'approved')
            ->whereDate('date', $date)
            ->paginate(5);
    } else {
        // Jika tidak ada `filteredDate`, ambil semua jadwal yang disetujui
        $schedules = UserRequest::where('id_mua', $id_mua)
            ->where('status', 'approved')
            ->paginate(5);
    }

    if ($schedules->isEmpty()) {
        return response()->json([
            'message' => 'No schedules available for this MUA',
        ], 200);
    }

    return response()->json([
        'last_page' => $schedules->lastPage(),
        'schedules' => ScheduleMuaResource::collection($schedules)
    ]);
}


    public function filteredSchedules(HttpRequest $request, $id_mua)
    {
        $filteredDate = $request->validate(['date' => 'required|date']);

        $date = Carbon::parse($filteredDate['date']);

        $schedules = UserRequest::where('id_mua', $id_mua)
            ->where('status', 'approved')
            ->whereDate('date', $date)
            ->paginate(5);

        if ($schedules->isEmpty()) {
            return response()->json([
                'message' => 'No schedules available for this MUA',
            ], 200);
        }

        return response()->json([
            'last_page' => $schedules->lastPage(),
            'schedules' => ScheduleMuaResource::collection($schedules)
        ]);
    }
}
