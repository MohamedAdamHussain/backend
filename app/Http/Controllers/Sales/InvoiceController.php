<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::all();
        return response()->json($invoices);
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
        $invoice = Invoice::create($request->validated());
        return response()->json($invoice, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        return response()->json($invoice);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $invoice->update($request->validated());
        return response()->json($invoice);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->json(null, 204);
    }

    /**
     * Mark invoice as paid
     */
    public function pay(Invoice $invoice)
    {
        $invoice->update(['status' => 'paid']);
        return response()->json($invoice);
    }

    /**
     * Cancel the invoice
     */
    public function cancel(Invoice $invoice)
    {
        $invoice->update(['status' => 'cancelled']);
        return response()->json($invoice);
    }
}
