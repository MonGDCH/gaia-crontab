<?php

declare(strict_types=1);

namespace process\crontab;

use Throwable;
use mon\log\Logger;
use mon\env\Config;
use Workerman\Worker;
use mon\util\Network;
use gaia\ProcessTrait;
use mon\util\Container;
use gaia\crontab\CrontabEnum;
use gaia\interfaces\ProcessInterface;
use Workerman\Connection\TcpConnection;

/**
 * 异步处理定时任务进程
 *
 * @author Mon <985558837@qq.com>
 * @version 1.0.0 2023-11-23
 */
class Task implements ProcessInterface
{
    use ProcessTrait;

    /**
     * 是否启用进程
     *
     * @return boolean
     */
    public static function enable(): bool
    {
        return Config::instance()->get('crontab.app.enable', false);
    }

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
        $log_channel = Config::instance()->get('ccrontab.app.log.channel', 'crontab');
        $log_config = Config::instance()->get('crontab.app.log.config', []);
        Logger::instance()->createChannel($log_channel, $log_config);
        Logger::instance()->setDefaultChannel($log_channel);
        // 数据库初始化
        // $config = Config::instance()->get('database', []);
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
            case CrontabEnum::TASK_TYPE['class']:
                // 类对象方法
                $result = $this->classHandler($event['data']);
                break;
            case CrontabEnum::TASK_TYPE['url']:
                // URl请求任务
                $result = $this->urlHandler($event['data']);
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
                $msg = $e->getMessage();
            }
        } else {
            $code = 0;
            $msg = "方法或类不存在";
        }

        return ['code' => $code, 'msg' => $msg];
    }

    /**
     * 发起URL请求
     *
     * @param array $query
     * @return array
     */
    protected function urlHandler(array $query): array
    {
        if (!isset($query['url']) || empty($query['url'])) {
            return ['code' => 0, 'msg' => '请求地址不能为空'];
        }
        try {
            $code = 1;
            $data = $query['data'] ?? [];
            $method = $query['method'] ?? 'GET';
            $header = $query['header'] ?? [];
            $timeOut = $query['timeOut'] ?? 5;
            $ua = $query['ua'] ?? '';
            $msg = Network::instance()->sendHTTP($query['url'], $data, $method, $header, false, $timeOut, $ua);
        } catch (Throwable $e) {
            $code = 0;
            $msg = $e->getMessage();
        }

        return ['code' => $code, 'msg' => $msg];
    }
}
