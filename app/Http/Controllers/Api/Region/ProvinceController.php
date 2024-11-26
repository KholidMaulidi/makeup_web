<?php

namespace App\Http\Controllers\Api\Region;

use App\Http\Controllers\Controller;
use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = 10;
        $data = Province::query();
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
        $result = $data->paginate($limit);
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
        $province = Province::find($id);
        if (!$province) {
            return response()->json(['data' => null, 'message' => 'Not Found!']);
        }
        return response()->json(['data' => $province, 'message' => 'success']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Province $Province)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Province $Province)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Province $Province)
    {
        //
    }
}
