<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "reference_no",
        "expense_category_id",
        "user_id",
        "cash_register_id",
        "amount",
        "note",
        "created_at",
        "deleted_at"
    ];

    public function expenseCategory()
    {
        return $this->belongsTo('App\Models\ExpenseCategory');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function cashRegister()
    {
        return $this->belongsTo('App\Models\CashRegister');
    }
}
