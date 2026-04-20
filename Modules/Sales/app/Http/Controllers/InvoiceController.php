<?php

namespace Modules\Sales\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Sales\Models\Invoice;
use Modules\Shared\Http\Traits\ApiResponse;
use Modules\Shared\Http\Traits\CreatesJournalEntry;

class InvoiceController extends Controller
{
    use ApiResponse, CreatesJournalEntry;

    public function index(): JsonResponse
    {
        Gate::authorize('view-invoices');
        return $this->successResponse(
            Invoice::with(['saleOrder.customer', 'items.product'])->paginate(20)
        );
    }

    public function show(Invoice $invoice): JsonResponse
    {
        Gate::authorize('view-invoices');
        return $this->successResponse(
            $invoice->load(['saleOrder.customer', 'items.product'])
        );
    }

    public function pay(Invoice $invoice, Request $request): JsonResponse
    {
        Gate::authorize('pay-invoices');

        if ($invoice->saleOrder->payment_status === 'paid') {
            return $this->errorResponse('Invoice already paid', 400);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($invoice, $validated) {
            $saleOrder     = $invoice->saleOrder;
            $newAmountPaid = $saleOrder->amount_paid + $validated['amount'];
            $totalAmount   = $saleOrder->total_amount;

            $paymentStatus = match(true) {
                $newAmountPaid >= $totalAmount => 'paid',
                $newAmountPaid > 0             => 'partial',
                default                        => 'unpaid',
            };

            $saleOrder->update([
                'amount_paid'    => min($newAmountPaid, $totalAmount),
                'payment_status' => $paymentStatus,
            ]);

            $this->createJournalEntry(
                'دفع فاتورة رقم ' . $invoice->id,
                $invoice,
                [
                    ['account_id' => $this->getAccountId('cash'),        'type' => 'debit',  'amount' => $validated['amount']],
                    ['account_id' => $this->getAccountId('receivables'), 'type' => 'credit', 'amount' => $validated['amount']],
                ]
            );
        });

        return $this->successResponse($invoice->fresh()->load('saleOrder'));
    }

    public function cancel(Invoice $invoice): JsonResponse
    {
        Gate::authorize('cancel-invoices');

        if ($invoice->saleOrder->payment_status === 'paid') {
            return $this->errorResponse('Cannot cancel a paid invoice', 400);
        }

        $invoice->saleOrder->update(['payment_status' => 'unpaid', 'amount_paid' => 0]);
        $invoice->delete();

        return $this->successResponse(null, 204);
    }
}
