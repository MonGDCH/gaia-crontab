<?php

declare(strict_types=1);

namespace support\crontab\task;

/**
 * 演示定时任务
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class DemoTask
{
    /**
     * 执行定时任务
     *
     * @return void
     */
    public function handler()
    {
        echo 'Hello Crontab Task: ' . date('Y-m-d H:i:s') . PHP_EOL;
        return 'Demo Task runing';
    }
}
