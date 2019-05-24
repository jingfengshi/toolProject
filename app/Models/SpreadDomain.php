<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Boolean;

class SpreadDomain extends Model
{

    protected $guarded=[];


    protected $casts=[
        'is_dead'=>'Boolean'
    ];


}
