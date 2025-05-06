<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffPermission extends Model
{

    protected $fillable = [
        'user_id', 'store_id',
        'manage_orders', 'manage_products', 'manage_settings'
    ];

    // علاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة مع المتجر
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
