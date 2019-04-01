<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poster extends Model
{
    protected  $fillable=['title','bg_image','code_image','poster_image','template_id'];

    public function setCodeImageAttribute($image)
    {
        if (is_array($image)) {
            $this->attributes['code_image'] = json_encode($image);
        }
    }

    public function getCodeImageAttribute($image)
    {
        return json_decode($image, true);
    }


    public function getPosterImageAttribute($image)
    {
        return json_decode($image, true);
    }
}
