<?php

namespace App\Console;

use Soli\Console\Command;

/**
 * 定义终端命令 task
 */
class Task extends Command
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
