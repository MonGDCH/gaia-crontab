<?php

declare(strict_types=1);

namespace gaia\crontab;

use support\Plugin;

/**
 * Gaia框架安装驱动
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Install
{
    /**
     * 标志为Gaia的驱动
     */
    const GAIA_PLUGIN = true;

    /**
     * 移动的文件
     *
     * @var array
     */
    protected static $file_relation = [
        'crontab.php'   => 'bin/crontab.php'
    ];

    /**
     * 移动的文件夹
     *
     * @var array
     */
    protected static $dir_relation = [
        'config'    => 'config/crontab',
        'support'   => 'support/crontab',
        'command'   => 'support/command/crontab'
    ];

    /**
     * 安装
     *
     * @return void
     */
    public static function install()
    {
        echo 'gaia-crontab installation successful, please execute `php gaia vendor:publish gaia\crontab`' . PHP_EOL;
    }

    /**
     * 更新升级
     *
     * @return void
     */
    public static function update()
    {
        echo 'gaia-crontab upgrade successful, please execute `php gaia vendor:publish gaia\crontab`' . PHP_EOL;
    }

    /**
     * 卸载
     *
     * @return void
     */
    public static function uninstall() {}

    /**
     * Gaia发布
     *
     * @return void
     */
    public static function publish()
    {
        // 创建框架文件
        $source_path = __DIR__ . DIRECTORY_SEPARATOR;
        // 移动文件
        foreach (static::$file_relation as $source => $dest) {
            $sourceFile = $source_path . $source;
            Plugin::copyFile($sourceFile, $dest, true);
        }
        // 移动目录
        foreach (static::$dir_relation as $source => $dest) {
            $sourceDir = $source_path . $source;
            Plugin::copydir($sourceDir, $dest, true);
        }
    }
}
