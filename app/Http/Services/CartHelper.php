<?php

namespace App\Http\Services;

use App\Http\Constants\StatusCodes;
use App\Models\Flavour;
use App\Models\FlavourVariation;
use App\Models\PublicUser;
use Illuminate\Support\Collection;
use App\Http\Services\ImageService;

class CartHelper
{

    /**
     * @var Flavour
     */
    private $flavours;

    /**
     * @var FlavourVariation
     */
    private $flavour_variations;


    /**
     * @var ImageService
     */
    private $image_service;


    public function __construct(Flavour $flavour, FlavourVariation $flavour_variations, ImageService $imageService)
    {
        $this->flavours = $flavour;
        $this->flavour_variations = $flavour_variations;
        $this->image_service = $imageService;
    }

    private function getMappedFlavours($cart = null, $request): array
    {
        $flavours = [];
        $mapped_flavours = [];

        if (isset($cart)) {

            $flavours = $this->image_service->transformFromCollection($this->flavours::whereIn('id', array_column($cart, 'flavour_id', 'flavour_id'))
                ->get()
                ->makeHidden(['image_gallery'])
                ->toArray(), $request);

            array_walk($flavours, function ($value, $key) use (&$mapped_flavours) {
                $value['description'] = strip_tags($value['description']);
                $value['flavour_variations'] = [];
                $mapped_flavours[$key] = $value;
            });

        }
        return $mapped_flavours;
    }

    /**
     * Calling variations and mapping quantity to them
     * @param null $cart
     * @return array
     */
    private function getMappedVariations($cart = null)
    {
        $cart_variations = array_column($cart, 'flavour_variation_id', 'flavour_variation_id');
        $matched_variations = $this->flavour_variations::whereIn('id', $cart_variations)->get();

        foreach ($cart as $index => &$item) {
            if ($matched_variations->contains('id', $item['flavour_variation_id'])) {
                $variation = $matched_variations->where('id', $item['flavour_variation_id'])->first();
                $variation->setQuantityAttribute($item['quantity']);
            }
        }

        return $matched_variations->toArray();
    }

    /**
     * @param $flavours
     * @param $flavour_variations
     * @param $cart
     * We get all flavours and by flavour id in variation we sort them out
     * $mapped_variations()->where , gives us all the result found in the collection
     */
    public function mapProducts($cart, $request)
    {

        $mapped_flavours = $this->getMappedFlavours($cart, $request);
        $mapped_variations = collect($this->getMappedVariations($cart));

        foreach ($mapped_flavours as $index_f => &$mapped_flavour) {
            if ($mapped_variations->contains('flavour_id', $mapped_flavour['id'])) {
                $mapped_flavour['flavour_variations'] = $mapped_variations->where('flavour_id', $mapped_flavour['id'])
                    ->sortBy('id')
                    ->values()
                    ->toArray();
            }
        }
        return $mapped_flavours;

    }
}
