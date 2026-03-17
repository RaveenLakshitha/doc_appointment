<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "reference_no",
        "user_id",
        "supplier_id",
        "item",
        "total_qty",
        "total_discount",
        "total_tax",
        "total_cost",
        "order_tax",
        "order_discount",
        "grand_total",
        "paid_amount",
        "status",
        "payment_status",
        "cash_register_id",
        "note",
        "created_at"
    ];

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function inventoryItem()
    {
        return $this->belongsTo('App\Models\InventoryItem', 'item');
    }

    public function cashRegister()
    {
        return $this->belongsTo('App\Models\CashRegister');
    }
}
