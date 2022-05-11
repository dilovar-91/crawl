<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnoProduct extends Model
{
    use HasFactory;
    protected $table = 'quke_products';
    protected $casts = [
        'pictures'   => 'json',
       // 'images'   => 'json',
      //  'new_images'   => 'json',
       'attributes'   => 'json',
       'color'   => 'json',
       'versions'   => 'json',
       'ram'   => 'json',
    ];
}
