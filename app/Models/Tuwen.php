<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tuwen extends Model
{
    protected $fillable=['title','desc','image_url','url'];

    public function taskMessage()
    {
        return $this->belongsTo(WechatTaskMessage::class,'task_message_id');
    }
}
