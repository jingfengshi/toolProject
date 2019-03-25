<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poster extends Model
{
    protected  $fillable=['title','bg_image','code_image','poster_image','template_id'];
}
