<?php

declare(strict_types=1);

namespace gaia\crontab;

/**
 * 定时任务相关枚举属性
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
interface CrontabEnum
{
    /**
     * 任务类型
     * 
     * @var array
     */
    const TASK_TYPE = [
        // 对象方法任务
        'class' => 0,
        // URL请求任务
        'url'   => 1,
    ];

    /**
     * 任务类型描述
     * 
     * @var array
     */
    const TASK_TYPE_TITLE = [
        // 类方法任务
        self::TASK_TYPE['class']    => '对象方法任务',
        // URL任务
        self::TASK_TYPE['url']      => 'URL请求任务',
    ];

    /**
     * 任务状态
     * 
     * @var array
     */
    const TASK_STATUS = [
        // 禁用
        'disable'   => 0,
        // 启用
        'enable'    => 1
    ];

    /**
     * 任务状态描述
     * 
     * @var array
     */
    const TASK_STATUS_TITLE = [
        // 禁用
        self::TASK_STATUS['disable']    => '禁用',
        // 启用
        self::TASK_STATUS['enable']     => '启用'
    ];

    /**
     * 是否单次任务状态
     * 
     * @var array
     */
    const SINGLETON_STATUS = [
        // 单次
        'once'  => 0,
        // 多次
        'more'  => 1
    ];

    /**
     * 是否单次任务状态描述
     * 
     * @var array
     */
    const SINGLETON_STATUS_TITLE = [
        // 单次
        self::SINGLETON_STATUS['once']  => '单次',
        // 多次
        self::SINGLETON_STATUS['more']  => '多次'
    ];

    /**
     * 记录任务日志状态
     * 
     * @var array
     */
    const TASK_LOG = [
        // 禁用
        'disable'   => 0,
        // 启用
        'enable'    => 1
    ];

    /**
     * 记录任务日志状态描述
     * 
     * @var array
     */
    const TASK_LOG_TITLE = [
        // 禁用
        self::TASK_LOG['disable']    => '关闭',
        // 启用
        self::TASK_LOG['enable']     => '开启'
    ];
}
