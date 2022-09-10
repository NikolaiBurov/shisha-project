<?php

namespace App\Listeners\Cart;

use App\Events\Cart\Update;
use Illuminate\Support\Facades\Cache;

class RemoveCartCache
{
    /**
     * Handle the event.
     *
     * @param Update $event
     * @return void
     */
    public function handle(Update $event)
    {
        $cacheKey = 'userID_' . $event->cartEntity->user_id;

        if (Cache::get($cacheKey)) {
            Cache::forget($cacheKey);
        }
    }
}
