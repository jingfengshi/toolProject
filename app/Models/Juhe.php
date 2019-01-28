<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Juhe extends Model
{

    const TYPE_AD = 'ad';
    const TYPE_JUHE = 'juhe';

    public static $typeMap = [
        self::TYPE_AD   => '单图广告',
        self::TYPE_JUHE => '多图聚合',
    ];


    public function setImagesAttribute($image)
    {
        if (is_array($image)) {
            $this->attributes['images'] = json_encode($image);
        }
    }

    public function getImagesAttribute($image)
    {
        return json_decode($image, true);
    }
}
