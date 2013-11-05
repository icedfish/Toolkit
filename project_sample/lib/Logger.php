<?php
//yubing@baixing.com

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
