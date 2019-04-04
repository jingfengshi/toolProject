<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameFaker extends Model
{
    public function setOriBannerImagesAttribute($image)
    {
        if (is_array($image)) {
            $this->attributes['ori_banner_images'] = json_encode($image);
        }
    }

    public function getOriBannerImagesAttribute($image)
    {
        return json_decode($image, true);
    }


    public function setOriContentImagesAttribute($image)
    {
        if (is_array($image)) {
            $this->attributes['ori_content_images'] = json_encode($image);
        }
    }

    public function getOriContentImagesAttribute($image)
    {
        return json_decode($image, true);
    }
}
