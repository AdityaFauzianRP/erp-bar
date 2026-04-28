<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    //
    protected $fillable = [
        'bank_name',
        'bank_code',
        'account_number',
        'account_holder',
        'branch',
        'initial_balance',
        'is_active',
    ];
}
