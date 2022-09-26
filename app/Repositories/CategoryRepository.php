<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use \Illuminate\Database\Eloquent\Builder;

class CategoryRepository
{
    public function findCategoriesByTerm(string $term): Builder
    {
        $term = strtolower($term);

        return Category::query()
            ->whereRaw('LOWER(`name`) = ? ', [$term]);
    }
}
