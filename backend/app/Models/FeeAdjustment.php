<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeAdjustment extends Model
{
    protected $fillable = [
        'neighborhood_id',
        'previous_amount',
        'new_amount',
        'effective_from',
        'reason',
    ];

    protected $casts = [
        'previous_amount' => 'float',
        'new_amount'      => 'float',
        'effective_from'  => 'date',
    ];

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }
}
