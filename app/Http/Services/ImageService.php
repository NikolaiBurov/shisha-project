<?php

namespace App\Http\Services;

use Illuminate\Http\Request;

class ImageService
{
    /**
     * @var
     */
    private $images_path;

    public function __construct($images_path)
    {
        $this->images_path = $images_path;
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

        foreach (explode(",",str_replace(['"',"[","]",'\\'],"",$data))  as $index => $item) {
            $result[] = $path .'/storage/'. $item;
        }

        return $result;
    }
}
