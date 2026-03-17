<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "code",
        "name",
        "is_active",
        "deleted_at"
    ];

    public function expense()
    {
        return $this->hasMany('App\Models\Expense');
    }
}
