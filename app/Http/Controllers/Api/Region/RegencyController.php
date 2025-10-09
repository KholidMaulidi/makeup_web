<?php

namespace App\Http\Controllers\Api\Region;

use App\Http\Controllers\Controller;
use App\Models\Regency;
use Illuminate\Http\Request;

class RegencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = 10;
        $data = Regency::query();
        if ($request->filled('limit') && is_numeric($request->limit)) {
            $limit = intval($request->limit);
        }
        if ($request->filled('type')) {
            $data->where('type', $request->type);
        }
        if ($request->filled('name')) {
            $data->where('name', 'like', "%$request->name%");
        }
        if ($request->filled('code')) {
            $data->where('code', $request->code);
        }
        if ($request->filled('full_code')) {
            $data->where('full_code', $request->full_code);
        }
        if ($request->filled('province_id')) {
            $data->where('province_id', $request->province_id);
        }
        $result = $data->with('province')->withCount('districts')->paginate($limit);
        return response()->json($result);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $regency = Regency::find($id);
        if (!$regency) {
            return response()->json(['data' => null, 'message' => 'Not Found!'], 404);
        }
        $regency->load('province');
        return response()->json(['data' => $regency, 'message' => 'success']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Regency $regency)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Regency $regency)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Regency $regency)
    {
        //
    }
}
