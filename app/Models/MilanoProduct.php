<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MilanoProduct extends Model
{
    use HasFactory;
    protected $table = 'milano_products_copy';

    protected $casts = [
        //'pictures'   => 'json',
       // 'images'   => 'json',
      //  'new_images'   => 'json',
       'img'   => 'json'
    ];
}
