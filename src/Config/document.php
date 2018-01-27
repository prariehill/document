<?php
return [
    /**
     * --------------------------------
     *
     * 输入 =￣ω￣=
     *
     * --------------------------------
     */
    'input' => [
        'namespace' => "App\\Model\\",
        'path' => base_path('app/Model'),
    ],

    /**
     * --------------------------------
     *
     * 输出 =￣ω￣=
     *
     * --------------------------------
     */
    'output' => [
        'model' => base_path('/doc/Model/Test')
    ],

    /**
     * --------------------------------
     * 数据库 =￣ω￣=
     * --------------------------------
     */
    'connections' => [
        'driver' => 'pdo_mysql',
        'dbname' => env('DB_DATABASE', config('database.connections.mysql.database')),
        'user' => env('DB_USERNAME', config('database.connections.mysql.username')),
        'password' => env('DB_PASSWORD', config('database.connections.mysql.password')),
        'host' => env('DB_HOST', config('database.connections.mysql.host')),
    ],
];