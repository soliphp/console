<?php
/**
 * @author ueaner <ueaner@gmail.com>
 */
namespace Soli\Console;

use Soli\Dispatcher as BaseDispatcher;
use Soli\Process;

class Dispatcher extends BaseDispatcher
{
    protected $handlerSuffix = '';

    /**
     * @param Command $handler
     * @param string $actionName
     * @param array $params
     * @return mixed
     */
    public function callAction($handler, string $actionName, array $params = [])
    {
        if (!$handler->getRunnable()) {
            return parent::callAction($handler, $actionName, $params);
        }

        return $this->callProcess($handler, $actionName, $params);
    }

    /**
     * 将 action 以多进程方式运行
     *
     * @param Command $handler
     * @param string $actionName
     * @param array $params
     */
    protected function callProcess($handler, $actionName, $params)
    {
        $process = new Process();
        $process->name = $handler->getName();
        $process->count = $handler->getCount();
        $process->daemonize = $handler->getDaemonize();
        $process->refork = $handler->getRefork();
        $process->logFile = $handler->getLogFile();

        $process->setJob(function ($worker) use ($handler, $actionName, $params) {
            // $worker 放到 action 参数的第一个位置
            array_unshift($params, $worker);
            call_user_func_array([$handler, $actionName], $params);
        });

        $process->start();
    }
}
