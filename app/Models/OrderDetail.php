<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    protected $guarded = ['id'];

    public function service()
    {
        return $this->belongsTo(Services::class, 'service_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function getQuantityAttribute(): int
    {
        return (int) ($this->attributes['qty_or_weight'] ?? 0);
    }
}
