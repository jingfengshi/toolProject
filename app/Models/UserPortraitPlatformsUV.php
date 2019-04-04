<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPortraitPlatformsUV extends Model
{
    protected $table = 'wechat_user_portrait_platformsuv';
    protected $fillable = ['gh_id', 'ref_date', 'iphone', 'android', 'other', 'created_at', 'updated_at'];

    public function wechat_applet()
    {
        return $this->hasOne(WechatApplet::class, 'gh_id', 'gh_id');
    }
}
