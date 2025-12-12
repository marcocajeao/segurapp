<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'neighborhood_id',
        'property_id',
        'monthly_fee_id',
        'method',
        'status',
        'amount',
        'paid_at',
        'mp_payment_id',
        'mp_preference_id',
        'reference',
        'created_by',
        'reviewed_by',
    ];

    protected $casts = [
        'amount'  => 'float',
        'paid_at' => 'datetime',
    ];

    public const METHOD_MP    = 'MERCADO_PAGO';
    public const METHOD_CASH  = 'CASH';
    public const METHOD_BANK  = 'BANK_TRANSFER';

    public const STATUS_PENDING        = 'PENDING';
    public const STATUS_PENDING_REVIEW = 'PENDING_REVIEW';
    public const STATUS_APPROVED       = 'APPROVED';
    public const STATUS_REJECTED       = 'REJECTED';
    public const STATUS_REFUNDED       = 'REFUNDED';

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function monthlyFee()
    {
        return $this->belongsTo(MonthlyFee::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
