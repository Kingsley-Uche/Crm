<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchModel extends Model
{
    //
    protected $table = 'branch_models';
    protected $fillable = [
        'name',
        'address',
        'contact_email',
        'contact_phone',
        'manager_name',
        'manager_email',
        'manager_phone',
        'account_name',
        'account_number',
        'bank_name',
    ];
}
