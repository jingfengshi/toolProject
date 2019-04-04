<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyVisitTrend extends Model
{
    protected $table = 'monthly_visit_trend';
    protected $fillable = ['gh_id', 'ref_date', 'session_cnt', 'visit_pv', 'visit_uv', 'visit_uv_new', 'stay_time_uv',
        'stay_time_session', 'visit_depth', 'created_at', 'updated_at'];

    public function wechat_applet()
    {
        return $this->hasOne(WechatApplet::class, 'gh_id', 'gh_id');
    }
}
