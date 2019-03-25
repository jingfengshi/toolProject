<?php

namespace App\Listeners;

use App\Events\CodeCreate;
use App\Handlers\DomainsToCode;
use App\Models\CodeItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class SaveCode
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CodeCreate  $event
     * @return void
     */
    public function handle(CodeCreate $event)
    {
        $code = $event->code;

        $codeToUrl=new DomainsToCode($code->urls);
        $res=$codeToUrl->formatUrls()->urlToCode();
        \Log::error(json_encode($res));
        $codes=trim($res['codes'],',');
        DB::table('codes')->where('id',$code->id)->update([
            'code_images'=>$codes
        ]);
        foreach ($res['arr'] as $key=>$item){
            $codeItem=new CodeItem();
            $codeItem->code_id=$code->id;
            $codeItem->url=$key;
            $codeItem->code_image=$item;
            $codeItem->save();
        }
    }
}
