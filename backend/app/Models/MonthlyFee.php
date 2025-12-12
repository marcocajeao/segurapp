<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyFee extends Model
{
    protected $fillable = [
        'neighborhood_id',
        'property_id',
        'period',
        'amount',
        'due_date',
        'status',
    ];

    protected $casts = [
        'amount'   => 'float',
        'period'   => 'date',
        'due_date' => 'date',
    ];

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_PAID    = 'PAID';
    public const STATUS_OVERDUE = 'OVERDUE';

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_OVERDUE;
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }
}
