<?php

namespace App\Http\Services;

use Illuminate\Http\Request;

class ImageService
{
    private static string $placeholder = '/storage/staticPictures/image_product_placeholder.svg';


    public static function getPlaceHolder(Request $request)
    {
        return $request->getSchemeAndHttpHost() . self::$placeholder;
    }

    public function transformFromCollection($obj, Request $request)
    {
        $path = $request->getSchemeAndHttpHost();

        foreach ($obj as $index => $item) {
            $obj[$index]['image'] = $path . '/storage/' . $item['image'];

        }
        return $obj;
    }

    /**
     * @param null $data
     * @param Request $request
     * @return string|void
     */
    public static function absolutePath($data = null, Request $request)
    {
        $path = $request->getSchemeAndHttpHost();

        if (isset($data) && is_object($data)) {
            $image = $path . '/storage/' . $data->image;
        } elseif (isset($data) && is_array($data)) {
            $image = $path . '/storage/' . $data['image'];
        } else {
            $image = $path . '/storage/' . $data;
        }


        $image = self::checkImageExists($image) !== false ? $image : self::getPlaceHolder($request);

        return $image;
    }

    public static function multipleImagesAbsolutePath($data, Request $request)
    {
        $path = $request->getSchemeAndHttpHost();
        $result = [];

        foreach (explode(",", str_replace(['"', "[", "]", '\\'], "", $data)) as $index => $item) {
            $result[] = $path . '/storage/' . $item;
        }

        return $result;
    }

    private static function checkImageExists($src): bool
    {
        return @getimagesize($src) !== false;
    }
}
