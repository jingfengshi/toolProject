<?php

namespace App\Admin\Extensions\tools;

use Encore\Admin\Admin;

class editButton
{

    protected $url;
    protected $name;

    public function __construct($url,$name)
    {

        $this->url=$url;
        $this->name=$name;
    }



    protected function render()
    {

        return "<a href='{$this->url}' class='btn btn-xs btn-success check-draw-money' >$this->name</a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}