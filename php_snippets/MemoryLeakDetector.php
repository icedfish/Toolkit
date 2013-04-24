<?php

/*
 * 用于调试和检测内存泄露问题
 *
 * 推荐用法： MemoryLeakDetector::inject(__CLASS__ . ':' . __LINE__);
 * */

class MemoryLeakDetector {

	private static $position, $usage;

	public static function inject($position) {
		$usage = memory_get_usage();
		if (self::$position) {
				if($usage != self::$usage) {
				$diff = $usage - self::$usage;
				echo "{$diff} / {$usage} (bytes) " . self::$position . " => {$position}\n";
			}
		}
		self::$position = $position;
		self::$usage = $usage;
	}
}
