<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'status', 'user_id', 'service_id', 'metadata_snapshot_service', 'metadata_snapshot_customer', 'snap_url_payment'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s',
        'metadata_snapshot_service' => 'array',
        'metadata_snapshot_customer' => 'array'
    ];

    public function orders_tracking()
    {
        return $this->hasMany('App\Models\OrderTracking')->orderBy('id', 'ASC');
    }

}
