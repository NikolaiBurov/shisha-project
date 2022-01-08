<?php

namespace App\Http\Services;

use Illuminate\Http\Request;

class ImageService
{

    public function transformFromCollection($obj, Request $request)
    {
        $path = $request->getSchemeAndHttpHost();

        foreach ($obj as $index => $item) {
            $obj[$index]['image'] = $path . '/storage/' . $item['image'];

            if (isset($item['image_gallery'])) {
                $obj[$index]['image_gallery'] = $this->multipleImagesAbsolutePath($item['image_gallery'], $request);
            }
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
            return $path . '/storage/' . $data->image;
        } elseif (isset($data) && is_array($data)) {
            return $path . '/storage/' . $data['image'];
        } else {
            return $path . '/storage/' . $data;
        }
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
}
