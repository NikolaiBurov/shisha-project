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
                $mapped_flavours[$key] = $value;
            });

        }

        return $mapped_flavours;
    }

    /**
     * With wherein query we make sure that only one variation is displayed with the added quantity
     * @param null $cart
     * @return array
     */
    private function getMappedVariations($cart = null)
    {
        $cart_variations = array_column($cart, 'flavour_variation_id', 'flavour_variation_id');
        $matched_variations = $this->flavour_variations::whereIn('id', $cart_variations)->get()->toArray();

        array_walk($cart, function ($value, $key) use (&$matched_variations) {
            foreach ($matched_variations as $key => $variation) {
                if ($value['flavour_variation_id'] === $variation['id']) {
                    $matched_variations[$key]['quantity'] = $value['quantity'];
                }
            }
        });

        return $matched_variations;
    }

    /**
     * @param $flavours
     * @param $flavour_variations
     * @param $cart
     */
    public function mapProducts($cart, $request)
    {
        $mapped_flavours = $this->getMappedFlavours($cart, $request);
        $mapped_variations = $this->getMappedVariations($cart);

        foreach ($mapped_flavours as $index_f => $mapped_flavour) {

            foreach ($mapped_variations as $index_v => $mapped_variation) {

                if ($mapped_flavour['id'] === $mapped_variation['flavour_id']) {
                    $mapped_flavours[$index_f]['variations'][] = $mapped_variation;
                }
            }
        }

        return $mapped_flavours;

    }
}
