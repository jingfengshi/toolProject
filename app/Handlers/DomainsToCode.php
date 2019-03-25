<?php
namespace App\Handlers;



use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DomainsToCode
{

    protected $urls;

    protected $urls_arr;

    public function __construct($urls)
    {
        $this->urls=$urls;
    }


    public function formatUrls()
    {
        $this->urls_arr=explode(',',$this->urls);

        return $this;

    }



    public function urlToCode()
    {
        if(empty($this->urls_arr)){
            throw  new \Exception('请先将urls格式化处理');
        }

        $new_urls=[];
        $new_urls['arr']=[];
        $new_urls['codes']='';
        $qrcode=QrCode::format('png')->size('200')->margin(1);
        foreach ($this->urls_arr as $item){
            $file_name='/qrcode/'.md5($item.time()).'.png';
            $qrcode->generate($item,'./upload'.$file_name);
            $rerult=file_get_contents('./upload'.$file_name);
            Storage::disk('qiniu')->put($file_name,$rerult);
            $new_urls['arr'][$item]=$file_name;
            $new_urls['codes'].=$file_name.',';
        }
        \Log::error($new_urls['codes']);

        return $new_urls;
    }

}