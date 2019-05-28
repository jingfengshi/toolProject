@component('mail::message')
# 入口域名死亡

死亡的域名为

@foreach($urls as $url)
    {{ $url }}
@endforeach


如有疑问请联系管理员24002310@qq.com
{{ config('app.name') }}
@endcomponent
