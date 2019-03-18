<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;

class WxShortDomain extends Model
{
    protected $table = 'wx_short_domain';

    public function creator(){
        return $this->belongsTo(Administrator::class,'admin_user_id');
    }
}
