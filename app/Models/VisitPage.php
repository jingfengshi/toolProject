<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitPage extends Model
{
    protected $table = 'visit_page';
    protected $fillable = ['gh_id', 'ref_date', 'page_path', 'page_visit_pv', 'page_visit_uv', 'page_staytime_pv', 'entrypage_pv',
        'exitpage_pv', 'page_share_pv', 'page_share_uv', 'created_at', 'updated_at'];

    public function wechat_applet()
    {
        return $this->hasOne(WechatApplet::class, 'gh_id', 'gh_id');
    }
}
