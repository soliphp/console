Soli PHP Console
----------------

Soli Console Component.

## 安装

使用 `composer` 进行安装：

    composer require soliphp/console

## 使用

如下我们编写 `test.php` 文件，内容为：

```php
<?php

namespace App\Console;

require __DIR__ . '/vendor/autoload.php';

/**
 * 定义终端命令 task
 */
class Task extends \Soli\Console\Command
{
    /**
     * 默认执行的方法：index
     */
    public function index()
    {
        return 'task:index';
    }

    public function handle()
    {
        return 'task:handle';
    }
}

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

$app = new \Soli\Console\App();

echo $app->handle();
```

执行 task 类中的 index 方法：

    php test.php task
    // 或
    php test.php task:index

执行 task 类中的 handle 方法：

    php test.php task:handle

## 示例

在 [examples] 文件夹下提供了一个带目录结构的终端命令例子，感兴趣的同学可以前去翻看。

    examples
    ├── app
    │   └── Console
    │       └── Task.php     > task命令文件
    ├── bootstrap.php        > 配置一些引导信息
    └── console              > 入口文件

运行方法：

    $ cd /path/to/soliphp/console/
    $ composer install
    $ php examples/console task
    $ php examples/console task:handle

## License

[MIT License]

[examples]: examples
[MIT License]: LICENSE
