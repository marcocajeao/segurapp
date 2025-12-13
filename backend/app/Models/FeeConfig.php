<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeConfig extends Model
{
    protected $fillable = [
        'neighborhood_id',
        'current_amount',
        'currency',
    ];

    protected $casts = [
        'current_amount' => 'float',
    ];

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }
}
