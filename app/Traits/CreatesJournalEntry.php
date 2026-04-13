<?php

namespace App;

use App\Models\JournalEntry;

// Trait مركزي
trait CreatesJournalEntry
{
    protected static function bootCreatesJournalEntry()
    {
        static::created(function ($model) {
            $accounts = $model->getJournalAccounts();

            $entry = JournalEntry::create([
                'reference_type' => get_class($model),
                'reference_id'   => $model->id,
                'date'           => now(),
            ]);

            // debit
            $entry->lines()->create([
                'account_id' => $accounts['debit'],
                'type'       => 'debit',
                'amount'     => $model->total_amount,
            ]);

            // credit
            $entry->lines()->create([
                'account_id' => $accounts['credit'],
                'type'       => 'credit',
                'amount'     => $model->total_amount,
            ]);
        });
    }
}

// كل Model يحدد حساباته
class Invoice extends Model
{
    use CreatesJournalEntry;

    public function getJournalAccounts()
    {
        return [
            'debit'  => Account::where('name', 'العملاء')->first()->id,
            'credit' => Account::where('name', 'المبيعات')->first()->id,
        ];
    }
}

class SupplyOrder extends Model
{
    use CreatesJournalEntry;

    public function getJournalAccounts()
    {
        return [
            'debit'  => Account::where('name', 'المشتريات')->first()->id,
            'credit' => Account::where('name', 'الموردون')->first()->id,
        ];
    }
}
