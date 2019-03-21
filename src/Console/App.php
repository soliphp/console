<?php
/**
 * @author ueaner <ueaner@gmail.com>
 */
namespace Soli\Console;

use Soli\App as BaseApp;

/**
 * 命令行应用
 */
class App extends BaseApp
{
    const ON_BOOT      = 'console.boot';
    const ON_FINISH    = 'console.finish';
    const ON_TERMINATE = 'console.terminate';

    /**
     * 默认核心服务
     */
    protected $coreServices = [
        'router'     => [\Soli\Console\Router::class, \Soli\RouterInterface::class],
        'dispatcher' => [\Soli\Console\Dispatcher::class, \Soli\Dispatcher::class, \Soli\DispatcherInterface::class],
        'events'     => [\Soli\Events\EventManager::class, \Soli\Events\EventManagerInterface::class],
    ];
}
