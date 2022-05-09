<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolfProduct extends Model
{
    use HasFactory;
    protected $table = 'automir_products';

    protected $casts = [
        'pictures'   => 'json'
    ];
}
