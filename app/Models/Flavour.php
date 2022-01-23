<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Models\Category;
use TCG\Voyager\Traits\Translatable;

class Flavour extends Model
{
    use HasFactory;
    use Translatable;

    protected $translatable = ['title', 'description', 'short_description'];

   protected $casts = [
        'image_gallery' => 'collection',
    ];

   /**
     * Get all of the variations for the flavour.
     */
    public function variations()
    {
        return $this->hasMany(FlavourVariation::class, 'flavour_id');
    }
}
