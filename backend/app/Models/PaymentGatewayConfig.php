<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewayConfig extends Model
{
    protected $fillable = [
        'neighborhood_id',
        'mp_public_key',
        'mp_access_token',
        'mp_webhook_secret',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }
}
