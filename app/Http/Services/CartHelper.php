<?php

namespace App\Http\Services;

use App\Models\Flavour;
use App\Models\FlavourVariation;

class CartHelper
{
    /**
     * @param $flavours
     * @param $flavour_variations
     * @param $cart
     * @todo refactor
     *
     */
    public function mapProducts($flavours, $flavour_variations, $cart)
    {
        $mapped_flavours = [];
        $mapped_variations = [];

        foreach ($cart as $item => $value) {

            foreach ($flavour_variations as $index => $flavour_variation) {
                if ($flavour_variation['id'] === $value['flavour_variation_id']) {

                    $flavour_variation['quanitity'] = $value['quantity'];
                    $mapped_variations[] = $flavour_variation;
                }

            }
            $mapped_variations = array_filter(array_unique($mapped_variations, SORT_REGULAR));

            foreach ($flavours as $index => $flavour) {
                if ($flavour['id'] == $value['flavour_id']) {
                    $mapped_flavours[] = $flavour;

                }
            }
            $mapped_flavours = array_filter(array_unique($mapped_flavours, SORT_REGULAR));

        }

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
