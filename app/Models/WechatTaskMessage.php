<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WechatTaskMessage extends Model
{
    const TYPE_WENZI = 'wenzi';
    const TYPE_TUWEN = 'tuwen';
    const TYPE_IMAGE = 'image';

    public static $typeMap = [
        self::TYPE_WENZI   => '文字',
        self::TYPE_TUWEN => '图文',
        self::TYPE_IMAGE => '图片',
    ];


    public function tuwens()
    {
        return $this->hasMany(Tuwen::class,'task_message_id');
    }
}
