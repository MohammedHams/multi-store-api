<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalServiceLog extends Model
{

    protected $fillable = [
        'order_id', 'service_type',
        'status', 'response', 'attempts'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
