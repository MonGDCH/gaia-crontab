CREATE TABLE IF NOT EXISTS `crontab` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '任务标题',
    `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '任务类型 0 class, 1 http',
    `rule` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '任务执行表达式',
    `target` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '调用任务字符串',
    `params` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '任务调用参数',
    `running_times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已运行次数',
    `sendlast_running_time_time` datetime DEFAULT NULL COMMENT '上次运行时间',
    `remark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
    `idx` tinyint(3) unsigned NOT NULL DEFAULT '50' COMMENT '排序权重',
    `singleton` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否单次执行: 0 是 1 不是',
    `savelog` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否记录日志: 0 否 1 是',
    `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '任务状态状态: 0禁用 1启用',
    `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期',
    `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建日期',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC COMMENT = '定时器任务表';

CREATE TABLE IF NOT EXISTS `crontab_log` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `crontab_id` int(11) unsigned NOT NULL COMMENT '任务id',
    `target` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '任务调用目标字符串',
    `params` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '任务调用参数',
    `result` text COLLATE utf8mb4_unicode_ci COMMENT '任务执行或者异常信息输出',
    `return_code` tinyint(1) unsigned NOT NULL COMMENT '执行返回状态: 0失败 1成功',
    `running_time` float unsigned NOT NULL COMMENT '执行所用时间',
    `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建日期',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `crontab_id` (`crontab_id`) USING BTREE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC COMMENT = '定时器任务执行日志表';