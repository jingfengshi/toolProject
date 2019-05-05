<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyWechatMiniVisit extends Model
{
    protected $table = 'daily_wechat_mini_visit';
    protected $fillable = ['gh_id', 'ref_date', 'enter_times', 'reply_times', 'create_at', 'update_at','appid'];
}
