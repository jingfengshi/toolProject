<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],
        'admin' => [
            'driver'     => 'local',
            'root' => storage_path('app/public'),
            'visibility' => 'public',
            'url' => env('APP_URL').'/storage',
        ],
        'sites' => [
            'driver'     => 'local',
            //'root'       => public_path('upload').'/sites',
            'root' => storage_path('app/public').'/sites',
            'visibility' => 'public',
            'url' => env('APP_URL').'/storage/sites/',
        ],

        'qiniu' => [
            'driver'  => 'qiniu',
            'domains' => [
                'default'   => env('QINIU_DOMAIN', 'xxx.clouddn.com'), //你的七牛域名
                'https'     => '',         //你的HTTPS域名
                'custom'    => '',                //你的自定义域名
            ],
            'access_key'=>  env('QINIU_ACCESS_KEY', 'xxxxxxxxxxxxxxxx'),  //AccessKey
            'secret_key'=> env('QINIU_SECRET_KEY', 'xxxxxxxxxxxxxxxx'),  //SecretKey
            'bucket'    =>  env('QINIU_BUCKET', 'test'),  //Bucket名字
            'notify_url'=> '',  //持久化处理回调地址
            'url'       => '',  // 填写文件访问根url
        ],


    ],

];
