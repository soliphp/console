<?php
/**
 * @author ueaner <ueaner@gmail.com>
 */
namespace Soli\Console;

use Soli\Component;

/**
 * 命令行任务基类
 *
 * @property \Soli\DispatcherInterface $dispatcher
 * @property \Soli\RouterInterface $router
 */
class Command extends Component
{
    /**
     * @var bool 决定了 action 是否以多进程方式运行
     */
    protected $runnable = false;

    public function getRunnable()
    {
        return $this->runnable;
    }

    public function getName()
    {
        return $this->name ?? '';
    }

    public function getCount()
    {
        return $this->count ?? 1;
    }

    public function getDaemonize()
    {
        return $this->daemonize ?? false;
    }

    public function getRefork()
    {
        return $this->refork ?? true;
    }

    public function getLogFile()
    {
        return $this->logFile ?? null;
    }
}
