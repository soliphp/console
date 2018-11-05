<?php

$autoloader = require dirname(__DIR__) . '/vendor/autoload.php';
$autoloader->addPsr4("App\\", __DIR__ . '/app');

// 配置容器
$container = new \Soli\Di\Container();
$container->set('router', function () {
    $router = new \Soli\Console\Router();
    $router->setDefaults([
        // 终端命令默认访问的命名空间
        'namespace' => "App\\Console\\",
    ]);
    return $router;
});
