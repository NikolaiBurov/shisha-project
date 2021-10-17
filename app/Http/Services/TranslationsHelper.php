<?php


namespace App\Http\Services;

use App\Http\Constants\StatusCodes;
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
        $response = [];

        foreach ($products as $item => $data) {
            if ($language == 'en') {
                if (!$data->translations->isEmpty()) {
                    $entities[$data->id] = $data->translate('en', 'bg');

                    $entities[$data->id]['image'] = ImageService::absolutePath($entities[$data->id]['image'], $request);

                    if (!is_null($data->image_gallery)) {
                        $entities[$data->id]['image_gallery'] = ImageService::multipleImagesAbsolutePath($entities[$data->id]['image_gallery'], $request);
                    }
                }

            } elseif ($language == 'bg') {

                $entities[$data->id] = $data->translate('bg', 'en');
                $entities[$data->id]['image'] = ImageService::absolutePath($entities[$data->id]['image'], $request);

                if (!is_null($data->image_gallery)) {
                    $entities[$data->id]['image_gallery'] = ImageService::multipleImagesAbsolutePath($entities[$data->id]['image_gallery'], $request);
                }

            }
        }

        foreach ($entities as $id => $collection) {
            $response[] = $collection;
        }
        return $response;


    }


    /**
     * @param $data
     * @param $language
     * @param Request $request
     * @param StatusCodes $statusCodes
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @todo Refacor this method in the future
     */
    public function filterHelper($data, $language, Request $request, StatusCodes $statusCodes,$current_page)
    {
        $translated = [];
        $result = [];
        foreach ($data->getCollection() as $item => $context) {
            if ($language == 'en') {
                if (!$context->translations->isEmpty()) {
                    $translated[$context->id] = $context->translate('en', 'bg');

                    $translated[$context->id]['image'] = ImageService::absolutePath($translated[$context->id]['image'], $request);
                }

            } elseif ($language == 'bg') {
                $translated[$context->id] = $context->translate('bg', 'en');

                $translated[$context->id]['image'] = ImageService::absolutePath($translated[$context->id]['image'], $request);

            }
        }

        foreach ($translated as $key => $value) {
            unset($value['image_gallery']);
            $result[] = $value;
        }

        $itemsTransformedAndPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $result,
            $data->total(),
            $data->perPage(),
            $current_page, [
                'path' => \Request::url(),
                'query' => [
                    'page' => $data->currentPage()
                ]
            ]
        );

        return $itemsTransformedAndPaginated;

    }
}
