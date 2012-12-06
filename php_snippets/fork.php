#!/bin/env php
<?php
/** A example denoted muti-process application in php
 * @filename fork.php
 * @touch date Wed 10 Jun 2009 10:25:51 PM CST
 * @author Laruence<laruence@baidu.com>
 * @license http://www.zend.com/license/3_0.txt PHP License 3.0
 * @version 1.0.1
 */

/** 确保这个函数只能运行在SHELL中 */
if (PHP_SAPI !== 'cli') {
    die("This Programe can only be run in CLI mode");
}

$pid = posix_getpid(); //取得主进程ID
$user = posix_getlogin(); //取得用户名

echo <<<EOD
USAGE: [command | expression]
input php code to execute by fork a new process
input quit to exit
EOD;

while (true) {
    $prompt = "\n{$user}$ ";
    $input = readline($prompt);

    readline_add_history($input);
    if ($input == 'quit') {
        break;
    }
    process_execute($input . ';');
}

exit(0);

function process_execute($input) {
    $pid = pcntl_fork(); //创建子进程
    if ($pid == 0) { //子进程进入了这个岔路口，父进程直接执行if后面的代码
        $pid = posix_getpid();
        echo "* Process {$pid} was created, and Executed:\n\n";
        eval($input); //解析命令
        exit;   //子进程必须退出，否则还会继续执行if后面的代码
    } else { //主进程
        $pid = pcntl_wait($status, WUNTRACED); //取得子进程结束状态
        if (pcntl_wifexited($status)) {
            echo "\n\n* Sub process: {$pid} exited with {$status}";
        }
    }
}