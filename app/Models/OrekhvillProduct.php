<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrekhvillProduct extends Model
{
    use HasFactory;

    protected $casts = [
        'pictures'   => 'json',
       // 'images'   => 'json',
      //  'new_images'   => 'json',
       'attributes'   => 'json'
    ];
}
