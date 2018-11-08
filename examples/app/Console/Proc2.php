<?php

namespace App\Console;

use Soli\Console\Command;
use Soli\Process;

/**
 * 终端命令结合多进程
 */
class Proc2 extends Command
{
    /** @var bool $runnable 这个属性决定了action是否以多进程方式运行 */
    protected $runnable = false;

    /** @var string $name 进程名称，只在 Linux 系统下起作用 */
    protected $name = 'soli console proc2';

    /** @var int $count 进程数 */
    protected $count = 4;

    /** @var bool $daemonize 是否以守护进程方式运行 */
    protected $daemonize = false;

    /** @var bool $refork 是否自动补充退出的进程 */
    protected $refork = false;

    /** @var string $logFile 记录进程的输出、终止等信息到此文件 */
    protected $logFile = '/tmp/soli-console-proc2.log';

    public function __construct()
    {
        // 针对不同的 action 可以选择是否使用多进程，以及指定不同的进程属性
        $action = $this->dispatcher->getActionName();
        switch ($action) {
            case 'command':
                $this->runnable = false;
                break;
            case 'process':
                $this->runnable = true;
                $this->logFile = null;
                break;
            case 'daemonize':
                $this->runnable = true;
                $this->daemonize = true;
                echo "Process output info in {$this->logFile}\n";
                break;
            case 'refork':
                $this->runnable = true;
                $this->refork = true;
                break;
        }
    }

    public function command($name = 'wukong')
    {
        echo "hello $name. just a command.\n";
    }

    /**
     * $worker 参数将成为 action 的第一个参数，$worker 参数之后是从命令行输入的参数
     */
    public function process(Process $worker, $name = 'wukong')
    {
        echo "hello $name. dump message from [worker:{$worker->id} {$worker->workerPid}] process.\n";
    }

    public function daemonize(Process $worker)
    {
        echo "dump message from [worker:{$worker->id} {$worker->workerPid}] daemonize process.\n";
    }

    public function refork(Process $worker)
    {
        echo "dump message from [worker:{$worker->id} {$worker->workerPid}] process.\n";
    }
}
