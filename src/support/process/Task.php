<?php

declare(strict_types=1);

namespace support\crontab\process;

use Throwable;
use mon\log\Logger;
use mon\env\Config;
use mon\thinkORM\ORM;
use Workerman\Worker;
use mon\util\Network;
use gaia\ProcessTrait;
use mon\util\Container;
use support\cache\CacheService;
use gaia\interfaces\ProcessInterface;
use Workerman\Connection\TcpConnection;
use gaia\crontab\driver\mixins\Variable;

/**
 * 异步处理定时任务进程
 *
 * @author Mon <985558837@qq.com>
 * @version 1.0.0 2023-11-23
 */
class Task implements ProcessInterface
{
    use ProcessTrait, Variable;

    /**
     * 获取进程配置
     *
     * @return array
     */
    public static function getProcessConfig(): array
    {
        return Config::instance()->get('crontab.app.process.task', []);
    }

    /**
     * 进程启动
     *
     * @param Worker $worker worker进程
     * @return void
     */
    public function onWorkerStart(Worker $worker): void
    {
        // 日志通道初始化
        $log_channel = Config::instance()->get('crontab.app.log.channel', 'crontab');
        $log_config = Config::instance()->get('crontab.app.log.config', []);
        Logger::instance()->createChannel($log_channel, $log_config);
        Logger::instance()->setDefaultChannel($log_channel);

        // 定义数据库配置，自动识别是否已安装ORM库
        if (class_exists(ORM::class)) {
            $config = Config::instance()->get('database', []);
            // 注册ORM
            $cache_store = class_exists(CacheService::class) ? CacheService::instance()->getService()->store() : null;
            ORM::register(false, $config, Logger::instance()->channel(), $cache_store);
        }
    }


    /**
     * 当客户端通过连接发来数据时触发的回调函数
     *
     * @param TcpConnection $connection
     * @param string $data
     * @return void
     */
    public function onMessage(TcpConnection $connection, string $data)
    {
        // 存活判断
        if ($data == 'ping') {
            $connection->send('pong');
            return;
        }
        $event = json_decode($data, true);
        switch ($event['type']) {
            case $this->getType('class'):
                // 类对象方法
                $result = $this->classHandler($event['data']);
                break;
            case $this->getType('http'):
                // HTTP请求任务
                $result = $this->httpHandler($event['data']);
                break;
            default:
                $result = ['code' => 0, 'msg' => '未支持的异步处理任务类型'];
                break;
        }

        $connection->send(json_encode($result, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 执行类对象方法
     *
     * @param array $class
     * @return array
     */
    protected function classHandler(array $class): array
    {
        if (class_exists($class['class']) && method_exists($class['class'], $class['method'])) {
            try {
                $code = 1;
                $params = $class['params'] ?? [];
                $msg = Container::instance()->invokeMethd([$class['class'], $class['method']], [$params]);
            } catch (Throwable $e) {
                $code = 0;
                $msg = 'Msg: ' . $e->getMessage() . ' , File: ' . $e->getFile() . ' , Line: ' . $e->getLine();
            }
        } else {
            $code = 0;
            $msg = "对象或方法不存在";
        }

        return ['code' => $code, 'msg' => $msg];
    }

    /**
     * 发起HTTP请求
     *
     * @param array $query
     * @return array
     */
    protected function httpHandler(array $query): array
    {
        if (!isset($query['url']) || empty($query['url'])) {
            return ['code' => 0, 'msg' => '请求地址不能为空'];
        }
        try {
            $code = 1;
            $data = $query['data'] ?? [];
            $method = $query['method'] ?? 'GET';
            $header = $query['header'] ?? [];
            $timeout = $query['timeout'] ?? 5;
            $ua = $query['ua'] ?? '';
            $msg = Network::instance()->sendHTTP($query['url'], $data, $method, $header, false, $timeout, $ua);
        } catch (Throwable $e) {
            $code = 0;
            $msg = $e->getMessage();
        }

        return ['code' => $code, 'msg' => $msg];
    }
}
