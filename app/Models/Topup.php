<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topup extends Model
{
    use HasFactory;
    protected $table = 'topup_history';

    protected $fillable = [
        'userId',
        'amount',
    ];
}
