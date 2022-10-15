<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Cart;
use Illuminate\Support\Facades\Cache;

class CartRepository
{
    /**
     * @var string
     */
    private string $cacheKey;

    /**
     * @param int $userID
     * @return int
     */
    public function findProductsInCartQuantity(int $userID): int
    {
        $this->cacheKey = 'userID_' . $userID;

        if (!Cache::get($this->cacheKey)) {
            Cache::rememberForever($this->cacheKey, function () use ($userID) {
                return Cart::query()
                    ->where('user_id', $userID)
                    ->count();
            });
        }

        return Cache::get($this->cacheKey);
    }
}


