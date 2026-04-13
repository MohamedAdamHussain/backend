<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\PriceList;
use Illuminate\Http\Request;

class PriceListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $priceLists = PriceList::all();
        return response()->json($priceLists);
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
        $priceList = PriceList::create($request->validated());
        return response()->json($priceList, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PriceList $priceList)
    {
        return response()->json($priceList);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PriceList $priceList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PriceList $priceList)
    {
        $priceList->update($request->validated());
        return response()->json($priceList);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PriceList $priceList)
    {
        $priceList->delete();
        return response()->json(null, 204);
    }
}
