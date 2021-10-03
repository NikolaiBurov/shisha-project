<?php


namespace App\Http\Services;

use App\Http\Services\ImageService;
use Illuminate\Http\Request;


class TranslationsHelper
{
    /**
     * @param string $language
     * @param $products
     * @param Request $request
     * @return array
     * By default entities are being saved on bg so we do not check if it exists
     */
    public function languangeMapper(string $language, $products, Request $request)
    {
        $entities = [];

        foreach ($products as $item => $data) {
            if ($language == 'en') {
                if ($data->translations->isEmpty()) {
                    $entities[$data->id] = null;
                } else {
                    $entities[$data->id] = $data->translate('en', 'bg');

                    $entities[$data->id]['image'] = ImageService::absolutePath($entities[$data->id]['image'], $request);

                }

            } elseif ($language == 'bg') {

                $entities[$data->id] = $data->translate('bg', 'en');
                $entities[$data->id]['image'] = ImageService::absolutePath($entities[$data->id]['image'], $request);

            }
        }

        return $entities;


    }
}
