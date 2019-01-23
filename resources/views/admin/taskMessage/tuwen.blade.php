<style>
    .msg-content {
        color: #7b7b7b;
    }
    img {
        vertical-align: middle;
    }
    .news-container {
        width: 360px;
        min-height: 200px;
        border: 1px solid #dadada;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15), 0 1px 5px rgba(0, 0, 0, 0.075);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15), 0 1px 5px rgba(0, 0, 0, 0.075);
        padding: 5px;
    }
    .main-news {
        border: 1px solid #dadada;
        border-radius: 4px;
        padding: 3px;
        align-content: center;
        position: relative;
        height: 159px;
    }
    .main-news {
        border: 1px solid #dadada;
        border-radius: 4px;
        padding: 3px;
        align-content: center;
        position: relative;
        height: 159px;
    }
    .main-news-img img {
        width: 340px;
    }
    .main-news-title-bg {
        margin-top: 120px;
        height: 30px;
        width: 340px;
        background-color: #000;
        opacity: 0.6;
        z-index: 1;
        position: absolute;
    }
    .main-news-title {
        margin-left: 5px;
        margin-top: 125px;
        color: #fff;
        font-size: 16px;
        z-index: 2;
        position: absolute;
        overflow: hidden;
    }
    p {
        margin: 0 0 10px;
    }
    .main-news-img {
        height: 150px;
        width: 340px;
        overflow: hidden;
        position: absolute;
        z-index: 0;
    }

    .normal-news {
        min-height: 40px;
        width: 340px;
        overflow: hidden;
    }
    a {
        color: #337ab7;
        text-decoration: none;
    }
    .news-title {
        width: 280px;
        float: left;
        padding: 5px 10px 5px 10px;
        font-size: 16px;
        color: #000000;
    }
    p {
        margin: 0 0 10px;
    }
    .news-pic {
        width: 60px;
        height: 60px;
        float: left;
        margin-top: 5px;
        overflow: hidden;
    }
    .news-pic img {
        width: 60px;
    }
</style>
<div class="content-container">
    <div class="msg-content">
        <div class="news-container">
            @foreach($tuwens as $tuwen)
                @if($loop->first)
                <div class="main-news">
                    <a href="{{$tuwen->url}}" target="_blank">
                        <div class="main-news-img">
                            <img src="{{$tuwen->image_url}}">
                        </div>
                        <div class="main-news-title-bg"></div>
                        <div class="main-news-title">
                            <p>{{$tuwen->title}}</p>
                        </div>
                    </a>
                </div>
                <hr>
                @else
                <div class="normal-news">
                    <a href="{{$tuwen->url}}" target="_blank">
                        <div class="news-title">
                            <p>{{$tuwen->title}}</p>
                        </div>
                        <div class="news-pic">
                            <img src="{{$tuwen->image_url}}">
                        </div>
                    </a>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
