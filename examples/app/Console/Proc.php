<?php

namespace App\Console;

use Soli\Console\Command;
use Soli\Process;

/**
 * 终端命令配合多进程
 *
 * 需使用 composer 安装 process 依赖：
 *
 *    composer require soliphp/process
 *
 */
class Proc extends Command
{
    /**
     * @var \Soli\Process
     */
    protected $process;

    public function __construct(Process $process)
    {
        $process->name = 'soli console';
        $process->count = 4;
        $process->daemonize = false;
        $process->logFile = '/tmp/soli-console-proc.log';

        $this->process = $process;
    }

    /**
     * 默认执行的方法：index
     */
    public function index()
    {
        $this->process->name .= ' proc:index';

        $this->process->setJob(function ($worker) {
            $this->doIndex($worker);
        });

        $this->process->start();

        return "return message from master process.\n";
    }

    protected function doIndex($worker)
    {
        echo "dump message from worker[{$worker->id} {$worker->workerPid}] process.\n";
    }
}
