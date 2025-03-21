<?php

declare(strict_types=1);

namespace gaia\crontab;

use mon\env\Config;
use mon\util\Instance;
use gaia\crontab\driver\Job;

/**
 * 任务管理器
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class TaskManage implements TaskInterface
{
    use Instance;

    /**
     * 定时任务驱动对象
     *
     * @var TaskInterface
     */
    protected $driver;

    /**
     * 构造方法
     */
    protected function __construct()
    {
        // 获取任务驱动对象，默认 Job 驱动
        $driver = Config::instance()->get('crontab.app.driver', Job::class);
        $this->driver = new $driver();
    }

    /**
     * 获取任务列表
     *
     * @return array
     */
    public function getTaskList(): array
    {
        return $this->driver->getTaskList();
    }

    /**
     * 获取指定的任务
     *
     * @param mixed $id
     * @return array
     */
    public function getTask($id): array
    {
        return $this->driver->getTask($id);
    }

    /**
     * 完成单次任务执行
     *
     * @param mixed $id
     * @return boolean
     */
    public function finishSingletonTask($id): bool
    {
        return $this->driver->finishSingletonTask($id);
    }

    /**
     * 更新任务最新执行信息
     *
     * @param mixed $id             任务ID
     * @param string $running_time  执行时间
     * @param integer $times        执行次数
     * @return boolean
     */
    public function updateTaskRunning($id, string $running_time, int $times = 1): bool
    {
        return $this->driver->updateTaskRunning($id, $running_time, $times);
    }

    /**
     * 记录任务日志
     *
     * @param array $log
     * @return boolean
     */
    public function recordTaskLog(array $log): bool
    {
        return $this->driver->recordTaskLog($log);
    }
}
