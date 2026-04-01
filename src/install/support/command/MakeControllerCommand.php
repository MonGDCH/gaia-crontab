<?php

declare(strict_types=1);

namespace gaia\command;

use mon\util\File;
use mon\console\Input;
use mon\console\Output;
use mon\console\Command;

/**
 * 生成指令类文件指令
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class MakeControllerCommand extends Command
{
    /**
     * 指令名
     *
     * @var string
     */
    protected static $defaultName = 'crontab:make-task';

    /**
     * 指令描述
     *
     * @var string
     */
    protected static $defaultDescription = 'Make crontab app task file util.';

    /**
     * 指令分组
     *
     * @var string
     */
    protected static $defaultGroup = 'mon-crontab';

    /**
     * 指令模板
     *
     * @var string
     */
    protected $cmd_tpl = <<<TPL
<?php

declare(strict_types=1);

namespace app\crontab;

/**
 * %s 定时任务处理器
 * 
 * Class %s
 * @author Mon <985558837@qq.com>
 * @copyright Gaia
 * @version 1.0.0 %s
 */
class %s
{
    /**
     * 执行定时任务
     *
     * @return void
     */
    public function handler()
    {
        return '%s Task runing';
    }
}
TPL;

    /**
     * 执行指令
     *
     * @param  Input  $in  输入实例
     * @param  Output $out 输出实例
     * @return mixed
     */
    public function execute(Input $input, Output $output)
    {
        $args = $input->getArgs();
        $now = date('Y-m-d');
        foreach ($args as $name) {
            $class = ucfirst($name);
            $className = $class . 'Task';
            // 创建进程文件
            $path = APP_PATH . DIRECTORY_SEPARATOR . 'crontab' . DIRECTORY_SEPARATOR . $className . '.php';
            if (file_exists($path)) {
                $output->write("Task `{$name}` file exists!");
                return;
            }
            $content = sprintf($this->cmd_tpl, $name, $class, $now, $className, $name);
            $save = File::createFile($content, $path, false);
            if (!$save) {
                $output->write("Make {$name} Task file faild!");
                continue;
            }

            $output->write("Make {$name} Task success!");
        }
    }
}
