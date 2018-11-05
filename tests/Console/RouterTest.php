<?php

namespace Soli\Tests\Console;

use PHPUnit\Framework\TestCase;

use Soli\Console\Router;

class RouterTest extends TestCase
{
    public function testDispatchDefaultHandle()
    {
        $router = new Router();
        $this->assertEquals('index', $router->getHandlerName());
        $this->assertEquals('index', $router->getActionName());
    }

    public function testDispatchWithArgv()
    {
        $argv = ['demo:make', 'param1', 'param2'];
        $router = new Router();
        $router->dispatch($argv);
        $this->assertEquals('demo', $router->getHandlerName());
        $this->assertEquals('make', $router->getActionName());
        $this->assertEquals(['param1', 'param2'], $router->getParams());
    }

    public function testDispatchWithServerArgv()
    {
        $_SERVER['argv'] = [__FILE__, 'demo:make', 'param1', 'param2'];
        $router = new Router();
        $router->dispatch();
        $this->assertEquals('demo', $router->getHandlerName());
        $this->assertEquals('make', $router->getActionName());
        $this->assertEquals(['param1', 'param2'], $router->getParams());
    }
}
