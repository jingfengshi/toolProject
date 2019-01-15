<?php
namespace App\Models;



use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;

class ShortDomain extends Model
{
    protected $table='short_domains';



    public function creator(){
        return $this->belongsTo(Administrator::class,'admin_user_id');
    }
}