<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class catProducto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombreCatProducto'
    ];
}
