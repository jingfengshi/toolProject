<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPortraitGenderUV extends Model
{
    protected $table = 'wechat_user_portrait_genderuv';
    protected $fillable = ['gh_id', 'ref_date', 'male', 'female', 'unknown', 'created_at', 'updated_at'];

    public function wechat_applet()
    {
        return $this->hasOne(WechatApplet::class, 'gh_id', 'gh_id');
    }
}
