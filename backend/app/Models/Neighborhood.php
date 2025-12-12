<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'default_due_day',
        'active',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function feeConfig()
    {
        return $this->hasOne(FeeConfig::class);
    }

    public function feeAdjustments()
    {
        return $this->hasMany(FeeAdjustment::class);
    }

    public function monthlyFees()
    {
        return $this->hasMany(MonthlyFee::class);
    }

    public function paymentGatewayConfig()
    {
        return $this->hasOne(PaymentGatewayConfig::class);
    }
}
