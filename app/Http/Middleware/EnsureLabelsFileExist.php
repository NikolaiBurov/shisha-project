<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use File;
class EnsureLabelsFileExist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $file_path = storage_path() . "/labels/labels.json";

        try {
            File::ensureDirectoryExists(storage_path() . "/labels/");

            if (!file_exists($file_path)) {
                file_put_contents($file_path, stripslashes(json_encode(['defualt' => 'default'], JSON_PRETTY_PRINT)));
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $next($request);

        return $next($request);
    }
}
