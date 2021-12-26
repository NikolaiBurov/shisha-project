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
            foreach ($cart as $index => $value) {
                $flavours[] = $value['flavour_id'];
            }
            $mapped_flavours = $this->image_service->transformFromCollection($this->flavours::whereIn('id', $flavours)
                ->get()
                ->makeHidden(['image_gallery'])
                ->toArray(), $request);

        }
        return $mapped_flavours;
    }

    private function getMappedVariations($cart = null)
    {
        $all_variations = $this->flavour_variations::all()->toArray();
        $mapped_variations = [];
        if (isset($cart)) {
            //todo
            foreach ($all_variations as $index => $all_variation) {
                foreach ($cart as $index => $item) {
                    if ($all_variation['id'] === $item['flavour_variation_id']) {
                        $all_variation['quantity'] = $item['quantity'];
                        $mapped_variations[] = $all_variation;
                    }

                }

            }
            $mapped_variations = array_filter(array_unique($mapped_variations, SORT_REGULAR));

        }
        return $mapped_variations;
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
                    $mapped_flavours[$index_f]['flavour_variations'][] = $mapped_variation;
                }
            }
        }

        return $mapped_flavours;

    }
}
