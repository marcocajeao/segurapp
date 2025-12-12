<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'neighborhood_id',
        'beneficiary_id',
        'code',
        'street',
        'number',
        'extra_address',
        'latitude',
        'longitude',
        'is_beneficiary',
        'qr_token',
        'active',
    ];

    protected $casts = [
        'latitude'      => 'float',
        'longitude'     => 'float',
        'is_beneficiary'=> 'boolean',
        'active'        => 'boolean',
    ];

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function monthlyFees()
    {
        return $this->hasMany(MonthlyFee::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
