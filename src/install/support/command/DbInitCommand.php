<?php

declare(strict_types=1);

namespace support\crontab\command;

use mon\env\Config;
use mon\thinkORM\Db;
use mon\console\Input;
use mon\console\Output;
use mon\console\Command;

/**
 * 定时任务数据库初始化安装
 *
 * @author Mon <98555883@qq.com>
 * @version 1.0.0
 */
class DbInitCommand extends Command
{
    /**
     * 指令名
     *
     * @var string
     */
    protected static $defaultName = 'crontab:dbinit';

    /**
     * 指令描述
     *
     * @var string
     */
    protected static $defaultDescription = 'init crontab database table';

    /**
     * 指令分组
     *
     * @var string
     */
    protected static $defaultGroup = 'mon-crontab';

    /**
     * 执行指令
     *
     * @param  Input  $in  输入实例
     * @param  Output $out 输出实例
     * @return integer  exit状态码
     */
    public function execute(Input $in, Output $out)
    {
        $contabSql = <<<SQL
CREATE TABLE IF NOT EXISTS `crontab` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '任务标题',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '任务类型: 0-class, 1-http',
  `rule` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '任务执行表达式',
  `singleton` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否单次执行: 0-多次 1-单次',
  `target` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '调用任务字符串',
  `params` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '任务调用参数',
  `running_times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已运行次数',
  `last_running_time` datetime DEFAULT NULL COMMENT '上次运行时间',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '任务状态状态: 0-禁用 1-启用',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建日期',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='定时器任务表';
SQL;

        $logSql = <<<SQL
CREATE TABLE IF NOT EXISTS `crontab_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `crontab_id` int(11) unsigned NOT NULL COMMENT '任务id',
  `running_time` float unsigned NOT NULL COMMENT '执行所用时间',
  `run_time` datetime NOT NULL COMMENT '执行任务时间',
  `status` tinyint(1) unsigned NOT NULL COMMENT '执行返回状态: 0-失败 1-成功',
  `result` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '任务执行结果描述',
  `target` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '任务调用目标字符串',
  `params` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '任务调用参数',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建日期',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `crontab_id` (`crontab_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='定时器任务执行日志表';
SQL;
        // 建表
        Db::setConfig(Config::instance()->get('database', []));
        Db::execute($contabSql);
        Db::execute($logSql);

        return $out->block('Init success!', 'SUCCESS');
    }
}
