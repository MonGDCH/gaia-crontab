<?php

declare(strict_types=1);

namespace gaia\crontab\driver;

use mon\env\Config;
use mon\log\Logger;
use mon\thinkORM\Db;
use mon\util\Validate;
use gaia\crontab\TaskInterface;
use gaia\crontab\driver\mixins\Variable;

/**
 * Mysql任务管理
 * 
 * @see 需要采用Think-ORM，需自行导入`crontab.sql`，并初始化数据库
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Mysql implements TaskInterface
{
    use Variable;

    /**
     * 任务表名
     *
     * @var string
     */
    protected $crontab = 'crontab';

    /**
     * 任务日志表名
     *
     * @var string
     */
    protected $log = 'crontab_log';

    /**
     * 验证器
     *
     * @var Validate
     */
    protected $validate;

    /**
     * 构造方法
     */
    public function __construct()
    {
        $this->validate = new Validate;
        $this->log = Config::instance()->get('crontab.log_table', 'crontab_log');
        $this->crontab = Config::instance()->get('crontab.task_table', 'crontab');
    }

    /**
     * 获取任务列表
     *
     * @return array
     */
    public function getTaskList(): array
    {
        $field = ['id', 'title', 'type', 'rule', 'target', 'params', 'singleton', 'status'];
        $data = Db::table($this->crontab)->where('status', $this->getStatus('enable'))->field($field)->json(['params'])->select()->toArray();

        return $data;
    }

    /**
     * 获取指定的任务
     *
     * @param mixed $id
     * @return array
     */
    public function getTask($id): array
    {
        $field = ['id', 'title', 'type', 'rule', 'target', 'params', 'singleton', 'status'];
        $info = Db::table($this->crontab)->where('status', $this->getStatus('enable'))->where('id', $id)->field($field)->json(['params'])->find();
        if (!$info) {
            return [];
        }

        return $info;
    }

    /**
     * 完成单次任务执行
     *
     * @param mixed $id
     * @return boolean
     */
    public function finishSingletonTask($id): bool
    {
        $save = Db::table($this->crontab)->where('id', $id)->update([
            'status' => $this->getStatus('disable'),
            'update_time' => $this->getTime()
        ]);
        if (!$save) {
            Logger::instance()->channel()->error('Save crontab task singleton info error');
            return false;
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
        $save = Db::table($this->crontab)->where('id', $id)->inc('running_times', $times)->data([
            'last_running_time' => $running_time,
            'update_time' => $this->getTime()
        ])->update();
        if (!$save) {
            Logger::instance()->channel()->error('Save crontab task running info error');
            return false;
        }

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
        $check = $this->validate->rule([
            'crontab_id'    => ['required', 'id'],
            'target'        => ['required', 'str'],
            'params'        => ['isset', 'str'],
            'result'        => ['isset', 'str'],
            'status'        => ['required', 'int', 'min:0'],
            'running_time'  => ['required', 'num'],
        ])->message([
            'crontab_id'    => '任务ID参数错误',
            'target'        => '请输入任务目标',
            'params'        => '请输入任务参数',
            'result'        => '任务响应参数错误',
            'status'        => '任务返回状态参数错误',
            'running_time'  => '请输入执行所用时间',
        ])->data($log)->check();
        if (!$check) {
            Logger::instance()->channel()->error('Record crontab task log error: ' . $this->validate->getError());
            return false;
        }

        $save = Db::table($this->log)->insert([
            'crontab_id'    => $log['crontab_id'],
            'target'        => $log['target'],
            'params'        => $log['params'],
            'result'        => $log['result'],
            'status'        => $log['status'],
            'running_time'  => $log['running_time'],
            'create_time'   => $this->getTime()
        ]);
        if (!$save) {
            Logger::instance()->channel()->error('Record crontab task log faild');
            return false;
        }

        return true;
    }

    /**
     * 获取时间
     *
     * @return void
     */
    protected function getTime()
    {
        $now = time();
        $format = Config::instance()->get('crontab.app.log_time_format', '');
        return $format ? date($format, $now) : $now;
    }
}
