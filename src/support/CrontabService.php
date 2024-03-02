<?php

declare(strict_types=1);

namespace support\crontab;

use mon\util\Network;
use mon\util\Instance;
use process\crontab\Server;

/**
 * 定时任务客户端服务
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class CrontabService
{
    use Instance;

    /**
     * 获取当前正在运行的任务
     *
     * @throws \Throwable    服务进程链接失败抛出异常
     * @return array
     */
    public function getPool(): array
    {
        $cammad = json_encode(['fn' => 'getPool', 'data' => []], JSON_UNESCAPED_UNICODE);
        $ret = static::communication($cammad);
        $data = json_decode($ret, true);
        if (!$data || $data['code'] != '1') {
            return [];
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
    public function reload(array $ids): bool
    {
        $cammad = json_encode(['fn' => 'reload', 'data' => $ids], JSON_UNESCAPED_UNICODE);
        $ret = static::communication($cammad);
        $data = json_decode($ret, true);
        if (!$data || $data['code'] != '1') {
            return false;
        }

        return true;
    }

    /**
     * 与定时任务服务进程通信
     *
     * @param string $messgae
     * @return string
     */
    public function communication(string $messgae = 'ping'): string
    {
        $host = Server::getListenHost();
        $port = Server::getListenPort();
        $result = Network::instance()->sendTCP($host, $port, $messgae . "\n", false);
        return trim((string)$result['result']);
    }
}
