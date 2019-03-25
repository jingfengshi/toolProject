<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable=['title','bg_width','bg_height','code_start_x','code_start_y','code_end_x','code_end_y','code_width','code_height'];
}
