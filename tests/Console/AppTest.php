<?php

namespace Soli\Tests;

use PHPUnit\Framework\TestCase;

use Soli\Console\App;
use Soli\Di\Container;

use Soli\Events\EventManager;
use Soli\Events\Event;
use Soli\Console\Router;

class AppTest extends TestCase
{
    /** @var \Soli\Console\App */
    protected $app;

    /** @var \Soli\Di\ContainerInterface */
    protected static $container;

    public static function setUpBeforeClass()
    {
        static::$container = new Container();
    }

    public static function tearDownAfterClass()
    {
        static::$container = null;
    }

    public function setUp()
    {
        static::$container->set('router', function () {
            $router = new Router();
            $router->setDefaults([
                'namespace' => "Soli\\Tests\\Handlers\\",
            ]);
            return $router;
        });

        $this->app = new App();
    }

    public function testHandleWithArgv()
    {
        $argv = ['task:handle', 'param1', 'param2'];
        $response = $this->app->handle($argv);
        $this->assertEquals('Hello, Soli.', $response);
    }

    public function testHandleWithServerArgv()
    {
        $_SERVER['argv'] = [__FILE__, 'task:handle', 'param1', 'param2'];

        $response = $this->app->handle();
        $this->assertEquals('Hello, Soli.', $response);
    }

    public function testTerminate()
    {
        $expected = App::ON_TERMINATE;
        $this->expectOutputString($expected);

        static::$container->set('events', function () {
            $events = new EventManager();
            $events->attach(App::ON_TERMINATE, function (Event $event) {
                echo $event->getName();
            });
            return $events;
        });

        $this->app->terminate();
    }
}
