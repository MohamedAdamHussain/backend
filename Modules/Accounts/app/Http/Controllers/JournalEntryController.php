<?php

namespace Modules\Accounts\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Accounts\Models\JournalEntry;
use Modules\shared\Http\Traits\ApiResponse;

class JournalEntryController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        Gate::authorize('view-journal-entries');
        return $this->successResponse(JournalEntry::with(['lines.account', 'user'])->paginate(20));
    }

    public function show(JournalEntry $journalEntry): JsonResponse
    {
        Gate::authorize('view-journal-entries');
        return $this->successResponse(
            $journalEntry->load(['lines.account', 'user', 'reference'])
        );
    }
}
