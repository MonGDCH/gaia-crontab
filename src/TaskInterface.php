<?php

declare(strict_types=1);

namespace gaia\crontab;

/**
 * 任务驱动接口
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
interface TaskInterface
{
    /**
     * 获取任务列表
     *
     * @return array
     */
    public function getTaskList(): array;

    /**
     * 获取指定的任务
     *
     * @param mixed $id
     * @return array
     */
    public function getTask($id): array;

    /**
     * 完成单次任务执行
     *
     * @param mixed $id
     * @return boolean
     */
    public function finishSingletonTask($id): bool;

    /**
     * 更新任务最新执行信息
     *
     * @param mixed $id
     * @param integer $running_time
     * @param integer $times
     * @return boolean
     */
    public function updateTaskRunning($id, int $running_time, int $times = 1): bool;

    /**
     * 记录任务日志
     *
     * @param array $log
     * @return boolean
     */
    public function recordTaskLog(array $log): bool;
}
