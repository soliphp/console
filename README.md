Soli PHP Console
----------------

Soli Console Component.

## Table of Contents

  * [安装](#安装)
  * [使用](#使用)
  * [编写多进程终端命令程序](#编写多进程终端命令程序)
  * [示例](#示例)
  * [License](#license)

## 安装

使用 `composer` 进行安装：

    composer require soliphp/console

## 使用

如下我们编写 `task.php` 文件，内容为：

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
    public function index($name = 'wukong')
    {
        return "hello $name, in task:index.\n";
    }

    /**
     * 命令行输入的参数将按顺序作为 action 的参数传入
     */
    public function handle($name = 'wukong', $alias = '孙行者')
    {
        return "hello $name <$alias>, in task:handle.\n";
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

1. 执行 Task 类中的 index 方法：

```
php task.php task
// 或
php task.php task:index
```

2. 执行 Task 类中的 handle 方法：

```
php task.php task:handle
```

3. 命令行输入的参数将按顺序作为 action 的参数传入，例如输入两个参数 `hulk` 和 `绿巨人` 执行：

```
php task.php task:handle hulk 绿巨人
```

将输出：

```
hello hulk <绿巨人>, in task:handle.
```

## 编写多进程终端命令程序

`Soli Console` 结合 [Soli Process] 可以让我们以编写终端命令程序的方式编写多进程程序，
同时复用项目中的已有的 Model / Service 等代码。

注意这里的多进程只支持在 `Unix-like` 系统下运行。

文件位置 [examples/app/Console/Proc2.php](examples/app/Console/Proc2.php)，内容为：

```php
<?php

namespace App\Console;

/**
 * 终端命令结合多进程
 */
class Proc2 extends \Soli\Console\Command
{
    /** @var bool $runnable 这个属性决定了action是否以多进程方式运行 */
    protected $runnable = false;

    /** @var string $name 进程名称，只在 Linux 系统下起作用 */
    protected $name = 'soli console proc2';

    /** @var int $count 进程数 */
    protected $count = 4;

    /** @var bool $daemonize 是否以守护进程方式运行 */
    protected $daemonize = false;

    /** @var bool $refork 是否自动补充退出的进程 */
    protected $refork = false;

    /** @var string $logFile 记录进程的输出、终止等信息到此文件 */
    protected $logFile = '/tmp/soli-console-proc2.log';

    public function __construct()
    {
        // 针对不同的 action 可以选择是否使用多进程，以及指定不同的进程属性
        $action = $this->dispatcher->getActionName();
        switch ($action) {
            case 'command':
                $this->runnable = false;
                break;
            case 'process':
                $this->runnable = true;
                $this->logFile = null;
                break;
            case 'daemonize':
                $this->runnable = true;
                $this->daemonize = true;
                echo "Process output info in {$this->logFile}\n";
                break;
            case 'refork':
                $this->runnable = true;
                $this->refork = true;
                break;
        }
    }

    public function command($name = 'wukong')
    {
        echo "hello $name. just a command.\n";
    }

    /**
     * $worker 参数将成为 action 的第一个参数，$worker 参数之后是从命令行输入的参数
     */
    public function process(\Soli\Process $worker, $name = 'wukong')
    {
        echo "hello $name. dump message from [worker:{$worker->id} {$worker->workerPid}] process.\n";
    }

    public function daemonize(\Soli\Process $worker)
    {
        echo "dump message from [worker:{$worker->id} {$worker->workerPid}] daemonize process.\n";
    }

    public function refork(\Soli\Process $worker)
    {
        echo "dump message from [worker:{$worker->id} {$worker->workerPid}] process.\n";
    }
}
```

与多进程相关的属性：

 属性名称  | 类型   | 默认值   | 描述
 ----------|--------|----------|-----------------------------------------
 runnable  | bool   | false    | 这个属性决定了action是否以多进程方式运行
 name      | string | ''       | 进程名称，只在 Linux 系统下起作用
 count     | int    | 1        | 进程数
 daemonize | bool   | false    | 是否以守护进程方式运行
 refork    | bool   | true     | 是否自动补充退出的进程
 logFile   | string | null     | 记录进程的输出、终止等信息的文件路径

设置 `runnable` 为 `true` 时，action 以多进程方式运行，`$worker 参数将成为 action 的第一个参数，$worker 参数之后是从命令行输入的参数`。

设置 `daemonize` 属性为 `true` 时，将以守护进程的方式运行，所有输出内容将被重定向到 `logFile` 属性所指定的文件，
未指定 `logFile` 属性时，输出内容将会被丢弃。

设置 `refork` 属性为 `true` 时，可以在进程退出（如程序出现异常）后自动补充新的进程。

下面执行 Proc2 类中的各个 action 方法，看看实际的执行效果：

1. 执行 Proc2 类中的 command 方法：

```
php examples/console proc2:command
```

将输出：

```
hello wukong. just a command.
```

2. 执行 Proc2 类中的 process 方法：

```
php examples/console proc2:process bajie
```

将输出类似以下内容（原始信息还会有进程启动、退出等相关信息，这里为了便于阅读，只贴出了 worker 进程中的输出信息）：

```
hello bajie. dump message from [worker:2 64163] process.
hello bajie. dump message from [worker:1 64162] process.
hello bajie. dump message from [worker:3 64164] process.
hello bajie. dump message from [worker:4 64165] process.
```

action 以多进程方式运行时：`$worker 参数将成为 action 的第一个参数，$worker 参数之后是从命令行输入的参数`，
如这里的方法定义，从第二个参数开始才是从终端输入的参数：

    public function process(\Soli\Process $worker, $name = 'wukong')

3. 执行 Proc2 类中的 daemonize 方法：

```
php examples/console proc2:daemonize
```

将输出：
```
Process output info in /tmp/soli-console-proc2.log
```

程序以守护进程的方式运行时，所有输出内容将被重定向到 `logFile` 属性所指定的文件，

查看 `/tmp/soli-console-proc2.log` 的文件内容为（原始信息还会有进程启动、退出等相关信息，这里为了便于阅读，只贴出了 worker 进程中的输出信息）：

```
dump message from [worker:2 64612] daemonize process.
dump message from [worker:3 64613] daemonize process.
dump message from [worker:1 64611] daemonize process.
dump message from [worker:4 64614] daemonize process.
```

4. 执行 Proc2 类中的 refork 方法：

```
php examples/console proc2:refork
```

将输出类似以下内容（原始信息还会有其他进程启动、退出等相关信息，这里为了便于阅读，只贴出了 `1 号 worker 进程`的相关信息）：

```
[2018-11-08 20:49:57] [master 73312] [worker:1 73332] process started
dump message from [worker:1 73332] process.
[2018-11-08 20:49:57] [master 73312] [worker:1 73332] process stopped with status 0
[2018-11-08 20:49:57] [master 73312] [worker:1 73339] process started
dump message from [worker:1 73339] process.
[2018-11-08 20:49:57] [master 73312] [worker:1 73339] process stopped with status 0
[2018-11-08 20:49:57] [master 73312] [worker:1 73343] process started
dump message from [worker:1 73343] process.
[2018-11-08 20:49:57] [master 73312] [worker:1 73343] process stopped with status 0
[2018-11-08 20:49:57] [master 73312] [worker:1 73346] process started
dump message from [worker:1 73346] process.
[2018-11-08 20:49:57] [master 73312] [worker:1 73346] process stopped with status 0
[2018-11-08 20:49:57] [master 73312] [worker:1 73350] process started
dump message from [worker:1 73350] process.
[2018-11-08 20:49:57] [master 73312] [worker:1 73350] process stopped with status 0
```

设置 `refork` 属性为 `true` 时，可以在进程退出后自动补充新的进程。当程序需要保持进程数，而由于异常进程被退出时，refork 将相当有用。

可以看到 `worker:1` 每次在执行完毕退出进程后，就会有新的进程 fork 出来，进行补充，保持进程数。

## 示例

在 [examples] 文件夹下提供了一个带目录结构的终端命令示例，感兴趣的同学可以直接翻看。

    examples
    ├── app
    │   └── Console
    │       ├── Proc.php     > proc  命令使用 \Soli\Process 多进程的原始类文件
    │       ├── Proc2.php    > proc2 命令结合 \Soli\Process 使用多进程的类文件
    │       └── Task.php     > task 命令类文件
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
