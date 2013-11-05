<?php

define('ROOT', __DIR__);
define('IS_CLI', PHP_SAPI == 'cli');

/*
	为了简化设计，不设置专门打Config类和文件了，数据库配置啥的直接放这里先拉。
*/

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'data');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

define('LOG_DIR', '/home/logs');

spl_autoload_register(function ($name) {
	$file = ROOT . '/lib/' . strtr($name, '\\', DIRECTORY_SEPARATOR) . '.php';
	if (file_exists($file)) {
		require $file;
	}
});
