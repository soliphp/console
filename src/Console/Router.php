<?php

namespace Soli\Console;

use Soli\RouterInterface;
use Soli\RouterTrait;

class Router implements RouterInterface
{
    use RouterTrait;

    public function __construct()
    {
        $this->defaultNamespaceName = "App\\Console\\";
    }

    /**
     * @param array $argv ['handler:action', 'param1', 'param2']
     */
    public function dispatch($argv = null)
    {
        if (empty($argv)) {
            $argv = array_slice($_SERVER['argv'], 1);
        }

        // handler:action
        if (isset($argv[0])) {
            $handler = explode(':', $argv[0]);
            $this->handlerName = $handler[0];

            if (isset($handler[1]) && $handler[1]) {
                $this->actionName = $handler[1];
            }
        }

        if (isset($argv[1])) {
            $this->params = array_slice($argv, 1);
        }
    }
}
