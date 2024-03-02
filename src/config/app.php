<?php

/*
|--------------------------------------------------------------------------
| 定时任务配置文件
|--------------------------------------------------------------------------
| 定义定时任务配置信息
|
*/

return [
    // 启用
    'enable'        => false,
    // 定时任务驱动对象
    'driver'        => \gaia\crontab\driver\Job::class,
    // 业务锁缓存前缀
    'lock_prefix'   => 'mon_crontab_',
    // 业务锁缓存时间
    'lock_expire'   => 600,
    // 定时任务进程配置
    'process'       => [
        // 主服务进程
        'server'    => [
            // 监听协议端口，采用text协议，方便通信
            'listen'        => 'text://127.0.0.1:7234',
            // 额外参数
            'context'       => [],
            // 进程数，服务主进程，只能1个
            'count'         => 1,
            // 通信协议，一般不需要修改
            'transport'     => 'tcp',
            // 进程用户
            'user'          => '',
            // 进程用户组
            'group'         => '',
            // 是否开启端口复用
            'reusePort'     => false,
            // 是否允许进程重载
            'reloadable'    => true
        ],
        // 任务处理进程
        'task'      => [
            // 监听协议端口，采用text协议，方便通信
            'listen'        => 'text://127.0.0.1:7235',
            // 额外参数
            'context'       => [],
            // 进程数，异步任务处理进程，按需配置
            'count'         => \gaia\App::cpuCount() * 4,
            // 通信协议，一般不需要修改
            'transport'     => 'tcp',
            // 进程用户
            'user'          => '',
            // 进程用户组
            'group'         => '',
            // 是否开启端口复用
            'reusePort'     => false,
            // 是否允许进程重载
            'reloadable'    => true,
        ],
    ],
    // 日志配置
    'log'           => [
        // 日志通道名
        'channel'   => 'crontab',
        // 通道配置
        'config'    => [
            // 解析器
            'format'    => [
                // 类名
                'handler'   => \mon\log\format\LineFormat::class,
                // 配置信息
                'config'    => [
                    // 日志是否包含级别
                    'level'         => true,
                    // 日志是否包含时间
                    'date'          => true,
                    // 时间格式，启用日志时间时有效
                    'date_format'   => 'Y-m-d H:i:s',
                    // 是否启用日志追踪
                    'trace'         => false,
                    // 追踪层级，启用日志追踪时有效
                    'layer'         => 3
                ]
            ],
            // 记录器
            'record'    => [
                // 类名
                'handler'   => \mon\log\record\FileRecord::class,
                // 配置信息
                'config'    => [
                    // 是否自动写入文件
                    'save'      => true,
                    // 写入文件后，清除缓存日志
                    'clear'     => true,
                    // 日志名称，空则使用当前日期作为名称       
                    'logName'   => '',
                    // 日志文件大小
                    'maxSize'   => 20480000,
                    // 日志目录
                    'logPath'   => RUNTIME_PATH . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'crontab',
                    // 日志滚动卷数   
                    'rollNum'   => 3
                ]
            ]
        ]
    ]
];
