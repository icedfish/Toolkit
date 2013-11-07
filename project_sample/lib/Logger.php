<?php
//yubing@baixing.com

if(!defined('LOG_DIR')) die('must define LOG_DIR before use this Logger class');

class Logger {
	public static function log($message, $filenamePrefix = 'logger') {
		@file_put_contents(
			LOG_DIR . '/' . $filenamePrefix . "_" . date("Y-m-d") . ".debug",
			date("H:i:s ") . "[" . getmypid() . "]: " . $message . "\n",
			FILE_APPEND
		);

		if (IS_CLI) {
			echo $message . PHP_EOL;
		}
	}
}
