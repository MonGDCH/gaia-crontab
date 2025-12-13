<?php

declare(strict_types=1);

namespace support\crontab;

use RuntimeException;
use mon\util\Network;
use gaia\crontab\TaskManage;
use support\crontab\process\Server;

/**
 * 定时任务客户端服务
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class CrontabService
{
    /**
     * 获取当前正在运行的任务
     *
     * @throws \Throwable    服务进程链接失败抛出异常
     * @return array
     */
    public static function getPool(): array
    {
        $cammad = json_encode(['fn' => 'getPool', 'data' => []], JSON_UNESCAPED_UNICODE);
        $ret = static::communication($cammad);
        $data = json_decode($ret, true);
        if (!$data || $data['code'] != '1') {
            throw new RuntimeException('获取运行中的任务失败：' . $data['msg']);
        }

        return $data['data'];
    }

    /**
     * 重载任务
     *
     * @param array $ids    任务ID列表
     * @throws \Throwable    服务进程链接失败抛出异常
     * @return boolean
     */
    public static function reload(array $ids): bool
    {
        $cammad = json_encode(['fn' => 'reload', 'data' => $ids], JSON_UNESCAPED_UNICODE);
        $ret = static::communication($cammad);
        $data = json_decode($ret, true);
        if (!$data || $data['code'] != '1') {
            throw new RuntimeException('重载任务失败：' . $data['msg']);
        }

        return true;
    }

    /**
     * 与定时任务服务进程通信
     *
     * @param string $messgae
     * @return string
     */
    public static function communication(string $messgae = 'ping'): string
    {
        $host = Server::getListenHost();
        $port = Server::getListenPort();
        $result = Network::sendTCP($host, $port, $messgae . "\n", false);
        return trim((string)$result);
    }

    /**
     * 获取任务管理器
     *
     * @return TaskManage
     */
    public static function getManage(): TaskManage
    {
        return TaskManage::instance();
    }
}
