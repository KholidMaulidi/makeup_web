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
    public function getSchedules()
    {
        $muaId = Auth::id();

        $schedules = UserRequest::where('id_mua', $muaId)
            ->where('status', 'approved')
            ->paginate(1);

        if ($schedules->isEmpty()) {
            return response()->json([
                'message' => 'No schedules available',
            ], 200);
        }

            return response()->json([
                'schedules' => ScheduleResource::collection($schedules)
            ]);
    }

    public function getMuaSchedules($id_mua)
    {
        $schedules = UserRequest::where('id_mua', $id_mua)
            ->where('status', 'approved')
            ->paginate(1);

        if ($schedules->isEmpty()) {
            return response()->json([
                'message' => 'No schedules available for this MUA',
            ], 200);
        }

        return response()->json([
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
            ->paginate(1);

        if ($schedules->isEmpty()) {
            return response()->json([
                'message' => 'No schedules available for this MUA',
            ], 200);
        }

        return response()->json([
            'schedules' => ScheduleMuaResource::collection($schedules)
        ]);
    }
}
