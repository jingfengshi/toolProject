<html>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<script type="text/javascript" src="https://js.users.51.la/19254449.js"></script>
<style>
    * { margin: 0; padding: 0 }
    iframe {
        width: 1px;
        min-width: 100%;
        *width: 100%;
        height:100%;
    }

    body {
        margin:0 auto;
    }

    body {
        position: fixed;
        top: 0px;
        bottom: 0px;
        width: 100%;
        height: 100%;
    }
</style>
<style>
    #erweima{
        display:none;
        opacity:0.01;
        position: absolute;
        top:0;
        bottom:0;
        right:0;
        left:0;
        height:100%;
        z-index:999;
    }
</style>
<body>
    <iframe id="aaaa1" frameborder="0" src="{{$origin_url}}" width="100%" height="100%"> </iframe>
    <img id="erweima"  src="">
</body>

<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script>

    $(function(){
        var u = navigator.userAgent;
        var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
        var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端

        if (isAndroid) {
            //$('#erweima').hide()
        }else{

            window.addEventListener('message', function(e) {
                var data = e.data
                if(typeof data == 'string'){
                    data = JSON.parse(data)
                }
                if(data.state=='1'){//显示二维码
                    $('#erweima').show()
                    $('#erweima').attr('src',data.url)
                }else{
                    $('#erweima').hide()
                }
            }, false);

        }


    })

</script>

</html>