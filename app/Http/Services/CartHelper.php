<?php

namespace App\Http\Services;

use App\Http\Constants\StatusCodes;
use App\Models\Flavour;
use App\Models\FlavourVariation;
use App\Models\PublicUser;
use Illuminate\Support\Collection;

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


    public function __construct(Flavour $flavour, FlavourVariation $flavour_variations)
    {
        $this->flavours = $flavour;
        $this->flavour_variations = $flavour_variations;
    }

    private function getMappedFlavours($cart = null): array
    {
        $flavours = [];
        $mapped_flavours = [];

        if (isset($cart)) {
            foreach ($cart as $index => $value) {
                $flavours[] = $value['flavour_id'];
            }
            $mapped_flavours = $this->flavours::whereIn('id', $flavours)->get()->toArray();
        }
        return $mapped_flavours;
    }

    private function getMappedVariations($cart = null)
    {
        $all_variations = $this->flavour_variations::all()->toArray();
        $mapped_variations = [];
        if (isset($cart)) {
            foreach ($cart as $index => $item) {
                foreach ($all_variations as $index => $variation) {
                    if ($variation['id'] === $item['flavour_variation_id']) {

                        $variation['quanitity'] = $item['quantity'];
                        $mapped_variations[] = $variation;
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
     * @todo refactor
     *
     */
    public function mapProducts($cart)
    {
        $mapped_flavours = $this->getMappedFlavours($cart);
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
