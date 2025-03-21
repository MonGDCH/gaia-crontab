<?php

declare(strict_types=1);

namespace gaia\crontab\driver\mixins;

use mon\env\Config;

/**
 * 配置变量获取器
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
trait Variable
{
    /**
     * 获取所有配置
     *
     * @return array
     */
    protected function getVariable(): array
    {
        return Config::instance()->get('crontab.app.variable', []);
    }

    /**
     * 获取状态值
     *
     * @param string $name  类型
     * @return integer
     */
    protected function getStatus(string $name = 'enable'): int
    {
        $default = $name == 'enable' ? 1 : 0;
        return (int)Config::instance()->get('crontab.app.variable.status.' . $name, $default);
    }

    /**
     * 获取任务类型值
     *
     * @param string $name  类型
     * @return integer
     */
    protected function getType(string $name = 'class'): int
    {
        $default = $name == 'class' ? 0 : 1;
        return (int)Config::instance()->get('crontab.app.variable.type.' . $name, $default);
    }

    /**
     * 获取执行频率值
     *
     * @param string $name  类型
     * @return integer
     */
    protected function getSingleton(string $name = 'once'): int
    {
        $default = $name == 'once' ? 1 : 0;
        return (int)Config::instance()->get('crontab.app.variable.singleton.' . $name, $default);
    }
}
