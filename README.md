Soli PHP Console
----------------

Soli Console Component.

Table of Contents
=================

  * [安装](#安装)
  * [使用](#使用)
  * [编写多进程终端命令程序](#编写多进程终端命令程序)
  * [示例](#示例)
  * [License](#license)

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

## 编写多进程终端命令程序

我们可以结合 [Soli Process] 编写多进程终端命令程序，
同时复用项目中的已有的 Model / Service 等代码。

文件位置 [examples/app/Console/Proc.php](examples/app/Console/Proc.php)，内容为：

```php
<?php

namespace App\Console;

class Proc extends \Soli\Console\Command
{
    /**
     * @var \Soli\Process
     */
    protected $process;

    public function __construct(\Soli\Process $process)
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
```

执行前在当前项目根目录使用 composer 安装 [Soli Process] 多进程包，注意只支持 `Unix-like` 系统：

    composer require soliphp/process

执行 proc 类中的 index 方法：

    php examples/console proc
    // 或
    php examples/console proc:index
    
将输出类似以下内容（原始信息还会有进程退出等相关信息，这里为阅读方便只贴出了 worker master 进程中的输出信息）：

    dump message from worker[2 56649] process.
    dump message from worker[1 56648] process.
    dump message from worker[3 56650] process.
    dump message from worker[4 56651] process.
    return message from master process.

## 示例

在 [examples] 文件夹下提供了一个带目录结构的终端命令例子，感兴趣的同学可以前去翻看。

    examples
    ├── app
    │   └── Console
    │       ├── Proc.php     > proc 命令文件
    │       └── Task.php     > task 命令文件
    ├── bootstrap.php        > 配置一些引导信息
    └── console              > 入口文件

运行方法：

    $ cd /path/to/soliphp/console/
    $ composer install
    $ php examples/console task
    $ php examples/console task:handle

## License

[MIT License]

[Soli Process]: https://github.com/soliphp/process
[examples]: examples
[MIT License]: LICENSE
