<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DayOff;
use App\Http\Resources\DayOffResource;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OffDayController extends Controller
{
    public function getAllDayOff()
    {
        $muaId = Auth::id();

        $dayOff = DayOff::where('id_mua', $muaId)->get();

        return response()->json([
            'data' => DayOffResource::collection($dayOff)
        ], 200);
    }
    public function setDayOff(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:byDate,byDay',
            'dates' => 'required_if:type,byDate|array', 
            'dates.*' => 'date', 
            'day' => 'required_if:type,byDay|string', 
        ]);

        $muaId = Auth::id();

        //Validate byDate
        if ($request->type === 'byDate') {
            $existingDates = [];

            foreach ($request->dates as $date) {
                $formattedDate = Carbon::parse($date)->format('Y-m-d');
                
                $exists = DayOff::where('id_mua', $muaId)
                                ->whereDate('date', $formattedDate)
                                ->exists();
                
                if ($exists) {
                    $existingDates[] = $formattedDate;
                } else {
                    DayOff::create([
                        'id_mua' => $muaId,
                        'date' => $formattedDate
                    ]);
                }
            }

            if (!empty($existingDates)) {
                return response()->json([
                    'message' => 'Some dates were already set as day off.',
                    'existing_dates' => $existingDates
                ], 400);
            }

            return response()->json(['message' => 'Day offs set successfully by date'], 201);
        }

        //Validate byDay
        if ($request->type === 'byDay') {
            $day = ucfirst(strtolower($request->day));
            $existingDates = [];

            for ($i = 0; $i < 4; $i++) {
                $nextDate = Carbon::now()->next($day)->addWeeks($i)->format('Y-m-d');
                
                $exists = DayOff::where('id_mua', $muaId)
                                ->whereDate('date', $nextDate)
                                ->exists();
                
                if ($exists) {
                    $existingDates[] = $nextDate;
                } else {
                    DayOff::create([
                        'id_mua' => $muaId,
                        'date' => $nextDate,
                    ]);
                }
            }

            if (!empty($existingDates)) {
                return response()->json([
                    'message' => 'Some dates were already set as day off.',
                    'existing_dates' => $existingDates
                ], 400);
            }

            return response()->json(['message' => 'Day offs set successfully by day'], 201);
        }
    }

    public function editDayOff(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $dayOff = DayOff::where('id_mua', Auth::id())->findOrFail($id);

        $newDate = Carbon::parse($request->date)->format('Y-m-d');

        $exist = DayOff::where('id_mua', Auth::id())
                ->whereDate('date', $newDate)
                ->exists();

        if ($exist) {
            return response()->json([
                'message' => 'The selected date is already set as a day off.'
            ], 400);
        }

        $dayOff->update([
            'date' => $newDate,
        ]);

        return response()->json(['message' => 'Day off updated successfully'], 200);
    }

    public function deleteDayOff($id)
    {
        $dayOff = DayOff::where('id_mua', Auth::id())->findOrFail($id);

        $dayOff->delete();

        return response()->json(['message' => 'Day off deleted successfully'], 200);
    }

}
