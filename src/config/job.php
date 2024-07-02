<?php

/*
|--------------------------------------------------------------------------
| 定时任务配置文件
|--------------------------------------------------------------------------
| 定义执行的定时任务，当定时任务驱动对象为 Job 时有效
|
*/

use gaia\crontab\CrontabEnum;

return [
    [
        // 任务名称
        'title'     => '测试对象方法任务',
        // 任务类型
        'type'      => CrontabEnum::TASK_TYPE['class'],
        // 定时执行规则
        'rule'      => '0/10 * * * * *',
        // 任务执行目标对象, 使用 @ 定义方法名，默认方法名 handler
        'target'    => \support\crontab\task\DemoTask::class,
        // 目标对象参数
        'params'    => [],
        // 是否单次执行
        'singleton' => CrontabEnum::SINGLETON_STATUS['more'],
        // 是否需要记录日志
        'savelog'   => CrontabEnum::TASK_LOG['disable'],
        // 是否有效
        'status'    => CrontabEnum::TASK_STATUS['enable'],
    ],
    [
        // 任务名称
        'title'     => '测试HTTP请求任务',
        // 任务类型
        'type'      => CrontabEnum::TASK_TYPE['http'],
        // 定时执行规则
        'rule'      => '5 0 * * * *',
        // 任务执行目标对象
        'target'    => 'https://gdmon.com/',
        // 目标对象参数
        'params'    => [
            // 请求方式
            'method'    => 'GET',
            // 请求头
            'header'    => [],
            // 请求数据
            'data'      => ['rc' => 'gaia'],
            // 请求超时时间
            'timeOut'   => 5,
            // 是否需要记录日志
            'ua'        => ''
        ],
        // 是否单次执行
        'singleton' => CrontabEnum::SINGLETON_STATUS['more'],
        // 是否需要记录日志
        'savelog'   => CrontabEnum::TASK_LOG['enable'],
        // 是否有效
        'status'    => CrontabEnum::TASK_STATUS['enable'],
    ]
];
