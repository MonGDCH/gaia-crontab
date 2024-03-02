<?php

declare(strict_types=1);

namespace gaia\crontab\driver;

use mon\log\Logger;
use think\facade\Db;
use mon\util\Validate;
use gaia\crontab\CrontabEnum;
use gaia\crontab\TaskInterface;

/**
 * Mysql任务管理
 * 
 * @see 需要采用Think-ORM，需自行导入`crontab.sql`，并初始化数据库
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Mysql implements TaskInterface
{
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
    }

    /**
     * 获取任务列表
     *
     * @return array
     */
    public function getTaskList(): array
    {
        $field = ['id', 'title', 'type', 'rule', 'target', 'params', 'singleton', 'savelog', 'status'];
        $list = Db::table($this->crontab)->where('status', CrontabEnum::TASK_STATUS['enable'])->field($field)->select();
        $data = [];
        foreach ($list as $item) {
            $item['params'] = json_decode($item['params'], true) ?: [];
            $data[] = $item;
        }

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
        $field = ['id', 'title', 'type', 'rule', 'target', 'params', 'singleton', 'savelog', 'status'];
        $info = Db::table($this->crontab)->where('status', CrontabEnum::TASK_STATUS['enable'])->where('id', $id)->field($field)->find();
        if (!$info) {
            return [];
        }

        $info['params'] = json_decode($info['params'], true) ?: [];
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
            'status' => CrontabEnum::TASK_STATUS['disable'],
            'update_time' => time()
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
     * @param integer $running_time 最近运行时间
     * @param integer $times        运行次数
     * @return boolean
     */
    public function updateTaskRunning($id, int $running_time, int $times = 1): bool
    {
        $save = Db::table($this->crontab)->where('id', $id)->inc('running_times', $times)->data([
            'last_running_time' => $running_time,
            'update_time' => time()
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
            'return_code'   => ['required', 'int', 'min:0'],
            'running_time'  => ['required', 'num'],
        ])->message([
            'crontab_id'    => '任务ID参数错误',
            'target'        => '请输入任务目标',
            'params'        => '请输入任务参数',
            'result'        => '任务响应参数错误',
            'return_code'   => '任务返回状态参数错误',
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
            'return_code'   => $log['return_code'],
            'running_time'  => $log['running_time'],
            'create_time'   => time()
        ]);
        if (!$save) {
            Logger::instance()->channel()->error('Record crontab task log faild');
            return false;
        }

        return true;
    }
}
