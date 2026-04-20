<?php

namespace Modules\Accounts\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Modules\Accounts\Models\Account;
use Modules\shared\Http\Traits\ApiResponse;

class AccountsController extends Controller
{
    use ApiResponse;

    public function index()
    {
        Gate::authorize('view-accounts');
        return $this->successResponse(Account::paginate(20));
    }

    public function store(Request $request)
    {
        Gate::authorize('create-accounts');
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:accounts,id',
            'name' => 'required|string',
            'slug' => 'required|string',
            'type' => 'required|string',
        ]);

        $account = Account::create($validated);
        return $this->successResponse($account, 204);
    }

    public function show(Account $account)
    {
        Gate::authorize('view-accounts');
        return $this->successResponse($account->load('children'));
    }

    public function update(Request $request, Account $account)
    {
        Gate::authorize('edit-accounts');
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:accounts,id',
            'name' => 'required|string',
            'slug' => 'required|string',
            'type' => 'required|string',
        ]);
        $account->update($validated);
        return $this->successResponse($account);
    }

    public function destroy(Account $account)
    {
        Gate::authorize('delete-accounts');
        $account->delete();
        return $this->successResponse(null, 204);
    }
}
