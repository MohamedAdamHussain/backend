<?php

// namespace Modules\Shared\Http\Traits;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Facades\Auth;
// use Modules\Accounts\Models\JournalEntry;

// trait CreatesJournalEntry
// {
//     protected function createJournalEntry(
//         string $description,
//         Model $reference,
//         array $lines  // [['account_id' => 1, 'type' => 'debit', 'amount' => 1000], ...]
//     ): void {
//         $entry = JournalEntry::create([
//             'created_by'     => Auth::id(),
//             'entry_date'     => now(),
//             'description'    => $description,
//             'reference_type' => get_class($reference),
//             'reference_id'   => $reference->id,
//         ]);

//         foreach ($lines as $line) {
//             $entry->lines()->create($line);
//         }
//     }
// }



namespace Modules\Shared\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Modules\Accounts\Models\Account;
use Modules\Accounts\Models\JournalEntry;

trait CreatesJournalEntry
{
    protected function createJournalEntry(
        string $description,
        Model $reference,
        array $lines
    ): void {
        // التحقق أن الـ debit يساوي الـ credit
        $totalDebit  = collect($lines)->where('type', 'debit')->sum('amount');
        $totalCredit = collect($lines)->where('type', 'credit')->sum('amount');

        if ($totalDebit !== $totalCredit) {
            throw new \Exception("Journal entry is not balanced: debit $totalDebit != credit $totalCredit");
        }

        $entry = JournalEntry::create([
            'created_by'     => Auth::id(),
            'entry_date'     => now(),
            'description'    => $description,
            'reference_type' => get_class($reference),
            'reference_id'   => $reference->id,
        ]);

        foreach ($lines as $line) {
            $entry->lines()->create($line);
        }
    }

    protected function getAccountId(string $slug): int
    {
        return Account::where('slug', $slug)->firstOrFail()->id;
    }
}
