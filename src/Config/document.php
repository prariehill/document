<?php
return [
    /**
     * --------------------------------
     *
     * 输入
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
     * 输出
     *
     * --------------------------------
     */
    'output' => [
        'model' => base_path('/doc/Model/Test')
    ],

    /**
     * --------------------------------
     *
     * 默认备注
     *
     * --------------------------------
     */
    'default' => [
        'model' => [
            'Member' => '用户'
        ],
        'column' => [
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'deleted_at' => '删除时间',
        ]
    ],
];