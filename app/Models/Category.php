<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use TCG\Voyager\Models\Category as VoyagerCategory;

class Category extends VoyagerCategory
{
    use HasFactory;

}

