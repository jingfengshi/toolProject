@component('mail::message')
    # 授权域名死亡

    死亡的域名为

    @foreach($urls as $url)
        {{ $url }}
    @endforeach

    ### 请及时更替

    如有疑问请联系管理员24002310@qq.com
    {{ config('app.name') }}
@endcomponent
