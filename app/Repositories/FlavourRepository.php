<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Flavour;
use Illuminate\Database\Eloquent\Builder;

class FlavourRepository
{
    public function findByTerm(string $term): Builder
    {
        $term = strtolower($term);

        return Flavour::query()
            ->whereRaw('LOWER(`title`) LIKE ? ', ['%' . $term . '%']);
    }
}

