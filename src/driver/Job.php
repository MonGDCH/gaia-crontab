<?php

declare(strict_types=1);

namespace gaia\crontab\driver;

use mon\env\Config;
use mon\log\Logger;
use gaia\crontab\TaskInterface;
use gaia\crontab\driver\mixins\Variable;

/**
 * 配置数组任务管理
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Job implements TaskInterface
{
    use Variable;

    /**
     * 任务数据
     *
     * @var array
     */
    protected $job = [];

    /**
     * 构造方法
     */
    public function __construct()
    {
        $this->job = Config::instance()->get('crontab.job', []);
    }

    /**
     * 获取任务列表
     *
     * @return array
     */
    public function getTaskList(): array
    {
        $list = [];
        foreach ($this->job as $id => $job) {
            if ($job['status'] == $this->getStatus('enable')) {
                $job['id'] = $id;
                $list[] = $job;
            }
        }
        return $list;
    }

    /**
     * 获取指定的任务
     *
     * @param mixed $id
     * @return array
     */
    public function getTask($id): array
    {
        $job = $this->job[$id] ?? [];
        if (!$job) {
            return $job;
        }

        $job['id'] = $id;
        return $job;
    }

    /**
     * 完成单次任务执行
     *
     * @param mixed $id
     * @return boolean
     */
    public function finishSingletonTask($id): bool
    {
        if (isset($this->job[$id])) {
            $this->job[$id]['status'] = $this->getStatus('disable');
        }

        return true;
    }

    /**
     * 更新任务最新执行信息
     *
     * @param integer $id           任务ID 
     * @param string  $running_time 最近运行时间
     * @param integer $times        运行次数
     * @return boolean
     */
    public function updateTaskRunning($id, string $running_time, int $times = 1): bool
    {
        return true;
    }

    /**
     * 记录任务日志
     *
     * @param array $log
     * @return boolean
     */
    public function recordTaskLog(array $log): bool
    {
        $log = 'Task[' . $log['crontab_id'] . '] runing, target: ' . $log['target'] . ', code: ' . $log['return_code'] . ', result: ' . $log['result'] . ', running_time: ' . $log['running_time'];
        Logger::instance()->channel()->info($log);
        return true;
    }
}
