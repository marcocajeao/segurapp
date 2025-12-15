<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

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
        'registered_at',
    ];

    protected $casts = [
        'latitude'        => 'float',
        'longitude'       => 'float',
        'is_beneficiary'  => 'boolean',
        'active'          => 'boolean',
        'registered_at'   => 'datetime',
    ];

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /* Helpers de dominio simples */

    public function isActiveBeneficiary(): bool
    {
        return $this->active && $this->is_beneficiary;
    }
}
