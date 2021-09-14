<?php

namespace App\Http\Services;
use Illuminate\Http\Request;
class ImageService
{
    /**
     * @var
     */
    private $images_path;

    public function  __construct($images_path){
        $this->images_path  = $images_path;
    }

    /**
     * @param null $data
     * @param Request $request
     * @return string|void
     */
    public static function absolutePath($data = null ,Request $request){
        $path = $request->getSchemeAndHttpHost();
        if(isset($data)){
            return $path . '/storage/' .  $data->image;
        }
    }
}
