<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    protected $fillable = [
        'name',
        'description',
        'discount',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(PriceListItem::class);
    }
}
