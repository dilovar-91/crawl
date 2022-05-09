<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class EboardDb extends Model
{
    use HasFactory;
    protected $table = 'eboard_db';
    public $timestamps = false;
}
