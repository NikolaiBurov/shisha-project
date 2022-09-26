<?php

namespace App\Models;

use App\Http\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use TCG\Voyager\Models\Category;
use TCG\Voyager\Traits\Translatable;

/**
 * @property Category|int $category
 */
class Flavour extends Model
{
    use HasFactory;
    use Translatable;

    /**
     * @var array|string[]
     */
    protected array $translatable = ['title', 'description', 'short_description'];

    /**
     * @var string[]
     */
    protected $casts = [
        'image_gallery' => 'collection'
    ];

    /**
     * @var string[]
     */
    protected $hidden = ['category_id'];

    /**
     * @var string[]
     */
    protected $appends = [
        'category'
    ];

    /**
     * @return mixed
     */
    public function getCategoryAttribute(): Category|int
    {
        return Category::find($this->getCategoryId()) ?? $this->getCategoryId();
    }

    /**
     * @return mixed|int
     */
    public function getCategoryId()
    {
        return $this->attributes['category_id'];
    }

    /**
     * @return string|null
     */
    public function getImageAttribute(): ?string
    {
        return ImageService::absolutePath($this->attributes['image'] ?? '' , Request::capture());
    }

    /**
     * Get all the variations for the flavour.
     */
    public function flavourVariations()
    {
        return $this->hasMany(FlavourVariation::class, 'flavour_id');
    }

    public function getFlavoursByRequest(Request $request)
    {
        return $this::with([
            'flavourVariations' => function ($query) use ($request) {
                if ($request->filled('price_from')) {
                    $query->where('price', '>', $request->get('price_from'));
                }
                if ($request->filled('price_to')) {
                    $query->where('price', '<=', $request->get('price_to'));
                }
                return $query;
            }
        ])
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
            ->when(
                ($request->filled('category_id') && !empty($request->get('category_id'))),
                function ($query) use ($request) {
                    $query->whereIn('category_id', $request->get('category_id'));
                }
            )
            ->orderBy('id', 'ASC')
            ->paginate($request->items_per_page);
    }
}
