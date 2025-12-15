<?php

declare(strict_types=1);

namespace app\crontab;

use mon\util\File;
use mon\log\Logger;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * 定时清除日志文件任务
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class ClearLogTask
{
    /**
     * 执行定时任务
     *
     * @return void
     */
    public function handler()
    {
        $logPath = RUNTIME_PATH . '/log';
        $clearLog = $this->getClearLog();
        Logger::instance()->channel()->info('Clear Befor ' . $clearLog . ' Log File Task Start');
        $dir_iterator = new RecursiveDirectoryIterator($logPath, RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
        $iterator = new RecursiveIteratorIterator($dir_iterator);
        /** @var RecursiveDirectoryIterator $iterator */
        foreach ($iterator as $file) {
            if ($file->getExtension() != 'log') {
                continue;
            }
            $date = substr($file->getBasename('.log'), 0, 8);
            if (is_numeric($date) && $date < $clearLog) {
                $filePath = $file->getPathname();
                Logger::instance()->channel()->info('Clear Log File: ' . $filePath);
                File::removeFile($filePath);
            }
        }
        Logger::instance()->channel()->info('Clear Befor ' . $clearLog . ' Log File Task End');
        return '清除' . $clearLog . '日前日志文件完成';
    }

    /**
     * 获取清除日志的时间
     *
     * @return integer
     */
    protected function getClearLog(): int
    {
        // 清除一个月前的日志
        $clearLog = date('Ym01', strtotime('-1 month'));
        return intval($clearLog);
    }
}
