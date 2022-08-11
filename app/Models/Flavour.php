<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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
    public function flavourVariations()
    {
        return $this->hasMany(FlavourVariation::class, 'flavour_id');
    }

    public function getFlavoursByRequest(Request $request)
    {
        return $this::with(['flavourVariations' => function ($query) use ($request) {

            if ($request->filled('price_from')) {
                $query->where('price', '>', $request->get('price_from'));
            }
            if ($request->filled('price_to')) {
                $query->where('price', '<=', $request->get('price_to'));
            }
            return $query;
        }])
            ->whereHas('flavourVariations', function ($query) use ($request) {
                if ($request->filled('price_from')) {
                    $query->where('price', '>', $request->get('price_from'));
                }
                if ($request->filled('price_to')) {
                    $query->where('price', '<=', $request->get('price_to'));
                }
                return $query;
            })
            ->when($request->filled('in_stock'), function ($query) use ($request) {
                $query->where('in_stock', '=', $request->get('in_stock'));
            })
            ->when(($request->filled('category_id') && !empty($request->get('category_id'))), function ($query) use ($request) {

                $query->whereIn('category_id', $request->get('category_id'));

            })
            ->orderBy('id', 'ASC')
            ->paginate($request->items_per_page);

    }
}
