<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">

        <li class="active">
            <a href="#tab-form-1" data-toggle="tab" aria-expanded="true">
                文字消息 <i class="fa fa-exclamation-circle text-red hide"></i>
            </a>
        </li>
        <li class="">
            <a href="#tab-form-2" data-toggle="tab" aria-expanded="false">
                图文消息 <i class="fa fa-exclamation-circle text-red hide"></i>
            </a>
        </li>
        <li class="">
            <a href="#tab-form-3" data-toggle="tab" aria-expanded="false">
                图片 <i class="fa fa-exclamation-circle text-red hide"></i>
            </a>
        </li>

    </ul>
    <div class="tab-content fields-group">

        <div class="tab-pane active" id="tab-form-1">
            <div class="form-group  ">
                <div class="col-sm-8">
                    <input type="hidden" name="message_type" value="1" required>
                    <textarea name="message_content" class="form-control message_content" rows="5"  placeholder="输入 消息内容"></textarea>


                </div>
            </div>

        </div>
        <div class="tab-pane" id="tab-form-2">
            <div class="form-group  ">
                <label for="tuwen_title" class="control-label">图文标题</label>

                <div class="col-sm-8">


                    <div class="input-group">

                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

                        <input type="text" id="tuwen_title" name="tuwen_title" value="" class="form-control tuwen_title" placeholder="输入 图文标题">


                    </div>


                </div>
            </div>
            <div class="form-group  ">

                <label for="tuwen_desc" class="  control-label">图文描述</label>

                <div class="col-sm-8">
                    <div class="input-group">

                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

                        <input type="text" id="tuwen_desc" name="tuwen_desc" value="" class="form-control tuwen_desc" placeholder="输入 图文描述">


                    </div>


                </div>
            </div>
            <div class="form-group  ">

                <label for="tuwen_image_url" class="control-label">图片url</label>

                <div class="col-sm-8">


                    <div class="input-group">

                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

                        <input type="text" id="tuwen_image_url" name="tuwen_image_url" value="" class="form-control tuwen_image_url" placeholder="输入 图片url">


                    </div>


                </div>
            </div>
            <div class="form-group  ">

                <label for="tuwen_url" class="control-label">url</label>

                <div class="col-sm-8">
                    <div class="input-group">

                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

                        <input type="text" id="tuwen_url" name="tuwen_url" value="" class="form-control tuwen_url" placeholder="输入 url">


                    </div>


                </div>
            </div>
        </div>
        <div class="tab-pane" id="tab-form-3">
            <div class="form-group  ">

                <label for="image_url" class="col-sm-2  control-label">Image url</label>

                <div class="col-sm-8">


                    <div class="file-input file-input-new"><div class="file-preview ">
                            <button type="button" class="close fileinput-remove" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>    <div class="file-drop-disabled">
                                <div class="file-preview-thumbnails">
                                </div>
                                <div class="clearfix"></div>    <div class="file-preview-status text-center text-success"></div>
                                <div class="kv-fileinput-error file-error-message" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="kv-upload-progress kv-hidden" style="display: none;"><div class="progress">
                                <div class="progress-bar bg-success progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                    0%
                                </div>
                            </div></div><div class="clearfix"></div>
                        <div class="input-group file-caption-main">
                            <div class="file-caption form-control  kv-fileinput-caption" tabindex="500">
                                <span class="file-caption-icon"></span>
                                <input class="file-caption-name" onkeydown="return false;" onpaste="return false;" placeholder="Select file...">
                            </div>
                            <div class="input-group-btn input-group-append">

                                <button type="button" tabindex="500" title="Abort ongoing upload" class="btn btn-default btn-secondary kv-hidden fileinput-cancel fileinput-cancel-button"><i class="glyphicon glyphicon-ban-circle"></i>  <span class="hidden-xs">Cancel</span></button>

                                <div tabindex="500" class="btn btn-primary btn-file"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">浏览</span><input type="file" class="image_url" name="image_url" id="1548061520222_42"></div>
                            </div>
                        </div></div>


                </div>
            </div>

        </div>

    </div>
</div>

