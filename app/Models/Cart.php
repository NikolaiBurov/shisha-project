<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'flavour_id',
        'flavour_variation_id',
        'quantity'
    ];
}
