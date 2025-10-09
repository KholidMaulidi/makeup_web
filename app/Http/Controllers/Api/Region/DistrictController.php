<?php

namespace App\Http\Controllers\Api\Region;

use App\Http\Controllers\Controller;
use App\Models\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = 10;
        $data = District::query();
        if ($request->filled('limit') && is_numeric($request->limit)) {
            $limit = intval($request->limit);
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
        if ($request->filled('regency_id')) {
            $data->where('regency_id', $request->regency_id);
        }
        if ($request->filled('code_regency')) {
            $data->whereRelation('regency', 'code', $request->code_regency);
        }
        if ($request->filled('province_id')) {
            $data->whereRelation('regency', 'province_id', $request->province_id);
        }
        if ($request->filled('code_province')) {
            $data->whereRelation('regency.province', 'code', $request->code_province);
        }
        $result = $data->with('regency.province')->paginate($limit);
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
    public function show($id)
    {
        $district = District::find($id);
        if (!$district) {
            return response()->json(['data' => null, 'message' => 'Not Found!'], 404);
        }
        $district->load('regency.province');
        return response()->json(['data' => $district, 'message' => 'success']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(District $district)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, District $district)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(District $district)
    {
        //
    }
}
