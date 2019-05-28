@component('mail::message')
    # 落地域名死亡

    死亡的域名为

    @foreach($urls as $url)
        {{ $url }}
    @endforeach

    ### 请及时更替

    如有疑问请联系管理员24002310@qq.com,<br>
    {{ config('app.name') }}
@endcomponent
