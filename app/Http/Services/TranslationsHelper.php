<?php


namespace App\Http\Services;

use App\Http\Constants\StatusCodes;
use App\Http\Services\ImageService;
use Illuminate\Http\Request;
use App\Models\FlavourVariation;
use Illuminate\Support\Arr;


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
        $flavours_variations = FlavourVariation::all()->toArray();

        foreach ($products as $item => $data) {

            if (!$data->translations->isEmpty()) {
                $entities[$data->id] = $data->translate($language, 'bg');


                $entities[$data->id]['flavour_variations'] = array_values(array_filter(array_map(function ($variations) use ($data) {
                    if ($variations['flavour_id'] === $data->id) {
                        return $variations;
                    }

                }, $flavours_variations)));

                $entities[$data->id]['image'] = ImageService::absolutePath($entities[$data->id]['image'], $request);

                if (!is_null($data->image_gallery) && !$data->image_gallery->isEmpty()) {
                    $entities[$data->id]['image_gallery'] = ImageService::multipleImagesAbsolutePath($entities[$data->id]['image_gallery'], $request);
                }
            }

        }

        return array_values($entities);

    }

    /**
     * @param $filters
     * @param $language
     * @param Request $request
     * @param StatusCodes $statusCodes
     * @param $current_page
     * @return array
     */
    public function translateFilteredResults($filters, $language, Request $request, StatusCodes $statusCodes, $current_page)
    {

        ///fix this method to separate results and pagiante them
        foreach ($filters as $index => $fitler) {
            $filters[$index]['title'] = $fitler->getTranslatedAttribute('title', $language, 'bg');
            $filters[$index]['description'] = $fitler->getTranslatedAttribute('description', $language, 'bg');
            $filters[$index]['short_description'] = $fitler->getTranslatedAttribute('short_description', $language, 'bg');
            $filters[$index]['image'] = ImageService::absolutePath($filters[$index]['image'], $request);
        }

        $result = $this->paginateFilters($filters, $current_page, $statusCodes);

        return $result;
    }

    /**
     * @param $filters
     * @param $current_page
     * @param StatusCodes $statusCodes
     * @return array
     */
    public function paginateFilters($filters, $current_page, StatusCodes $statusCodes)
    {
        $paginatior = new \stdClass();
        $flavours = [];

        $itemsTransformedAndPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $filters,
            $filters->total(),
            $filters->perPage(),
            $current_page, [
                'path' => \Request::url(),
                'query' => [
                    'page' => $filters->currentPage()
                ]
            ]
        );
        foreach ($itemsTransformedAndPaginated['data'] as $index => $datum) {
            unset($datum['image_gallery']);
            if(isset($datum['translations']))
                unset($datum['translations']);
            $flavours[] = $datum;
        }
        $status_code = empty($flavours)
            ? array_keys(get_object_vars($statusCodes->postRequests()))[4]
            : array_keys(get_object_vars($statusCodes->postRequests()))[0];

        $paginatior->on_first_page = $itemsTransformedAndPaginated->onFirstPage();
        $paginatior->last_page = $itemsTransformedAndPaginated->lastPage();
        $paginatior->total_products = $itemsTransformedAndPaginated->total();
        $paginatior->current_page = $itemsTransformedAndPaginated->currentPage();
        $paginatior->per_page = $itemsTransformedAndPaginated->perPage();

        return ['paginator' => $paginatior, 'data' => $flavours, 'status_code' => $status_code];
    }

    /**
     * @param $data
     * @param $language
     * @param Request $request
     * @param StatusCodes $statusCodes
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @todo Refacor this method in the future
     */
    public function paginatorHelper($data, $language, Request $request, StatusCodes $statusCodes, $current_page)
    {
        $translated = [];
        $flavours_variations = FlavourVariation::all()->toArray();

        foreach ($data->getCollection() as $item => $context) {

            if (!$context->translations->isEmpty()) {
                $translated[$context->id] = $context->translate($language, 'bg');

                $translated[$context->id]['image'] = ImageService::absolutePath($translated[$context->id]['image'], $request);

                $translated[$context->id]['flavour_variations'] = array_values(array_filter(array_map(function ($variations) use ($context) {
                    if ($variations['flavour_id'] === $context->id) {
                        return $variations;
                    }

                }, $flavours_variations)));

            }
        }
        $result = $this->formatedResults($data, $translated, $current_page, $statusCodes);

        return $result;

    }

    private function formatedResults($data, $items, $current_page, StatusCodes $statusCodes)
    {
        $paginatior = new \stdClass();
        $flavours = [];

        $itemsTransformedAndPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $data->total(),
            $data->perPage(),
            $current_page, [
                'path' => \Request::url(),
                'query' => [
                    'page' => $data->currentPage()
                ]
            ]
        );

        foreach ($itemsTransformedAndPaginated as $key => $value) {
            unset($value['image_gallery']);
            $flavours[] = $value;
        }

        $status_code = empty($flavours)
            ? array_keys(get_object_vars($statusCodes->postRequests()))[4]
            : array_keys(get_object_vars($statusCodes->postRequests()))[0];

        $paginatior->on_first_page = $itemsTransformedAndPaginated->onFirstPage();
        $paginatior->last_page = $itemsTransformedAndPaginated->lastPage();
        $paginatior->total_products = $itemsTransformedAndPaginated->total();
        $paginatior->current_page = $itemsTransformedAndPaginated->currentPage();
        $paginatior->per_page = $itemsTransformedAndPaginated->perPage();

        return ['paginator' => $paginatior, 'data' => $flavours, 'status_code' => $status_code];


    }
}
