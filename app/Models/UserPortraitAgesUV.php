<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPortraitAgesUV extends Model
{
    protected $table = 'wechat_user_portrait_agesuv';
    protected $fillable = ['gh_id', 'ref_date', 'under17', 'age18_24', 'unknown', 'age25_29', 'age30_39', 'age40_49', 'over50', 'created_at', 'updated_at'];

    public function wechat_applet()
    {
        return $this->hasOne(WechatApplet::class, 'gh_id', 'gh_id');
    }
}
