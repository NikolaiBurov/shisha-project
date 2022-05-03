<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Flavour;

class FlavourVariation extends Model
{
    use HasFactory;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function flavour()
    {
        return $this->belongsTo(Flavour::class);
    }

    public function setQuantityAttribute(int $quantity) : void
    {
         $this->attributes['quantity'] = $quantity;
    }
}
