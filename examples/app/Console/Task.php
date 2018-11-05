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
    public function index()
    {
        return 'task:index';
    }

    public function handle()
    {
        return 'task:handle';
    }
}
