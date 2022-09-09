<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Cart;
use Illuminate\Support\Facades\Cache;

class CartRepository
{
    public function findProductsInCartQuantity(int $userID): int
    {
        //maybe cache this query
        return Cart::query()
            ->where('user_id', $userID)
            ->count();
    }
}

