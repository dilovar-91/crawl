<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;
    protected $table = 'orekhvill_links';
    protected $casts = [
        'pictures'   => 'json'
    ];
}
