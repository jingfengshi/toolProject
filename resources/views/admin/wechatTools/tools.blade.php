<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<style>
    .vertical-layout{
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
    }

    .vertical-layout img{
        width: 100%;
    }
</style>
<body>
<div class="vertical-layout">
    @foreach($images as $image)
        <img src="{{URL::asset('upload/'.$image)}}" >
    @endforeach
</div>
</body>

<script>
    window.onload = function()
    {
        var aLi = document.getElementsByTagName("img");
        for(var i = 0; i < aLi.length; i++)
        {
            aLi[i].onclick = function()
            {
                window.location.href='{{$url}}';
            }
        }
    }
</script>
</html>
