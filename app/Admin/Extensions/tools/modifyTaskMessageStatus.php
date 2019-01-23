<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;

class modifyTaskMessageStatus
{
    protected $id;

    protected $status;

    public function __construct($id,$status)
    {
        $this->id = $id;
        $this->status=$status;
    }

    protected function script()
    {

        return <<<SCRIPT

$('.modify-task-status').on('click', function () {
     var id = $(this).data('id');
     var status=$(this).data('status');
 
     swal({
        title: "确认"+status+"吗?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "确认",
        showLoaderOnConfirm: true,
        cancelButtonText: "取消",
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                    method: 'post',
                    url: '/admin/wechat/taskMessage/status/' + id,
                    data: {
                        _method:'put',
                        _token:LA.token,
                    },
                    success: function (data) {
                        $.pjax.reload('#pjax-container');

                        resolve(data);
                    }
                });
            });
        }
    }).then(function(result) {
        var data = result.value;
        if (typeof data === 'object') {
            if (data.status) {
                swal(data.message, '', 'success');
            } else {
                swal(data.message, '', 'error');
            }
        }
    });
});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());
        if($this->status=='开启'){
            $class='btn-info';
        }else{
            $class='btn-danger';
        }
        return "<a class='btn btn-xs ".$class." modify-task-status' data-id='{$this->id}' data-status='{$this->status}'>$this->status</a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}