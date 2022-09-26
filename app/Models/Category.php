<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use TCG\Voyager\Models\Category as VoyagerCategory;

class Category extends VoyagerCategory
{
    use HasFactory;

    /**
     * @return HasMany
     */
    public function flavours(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Flavour::class, 'category_id');
    }

}

