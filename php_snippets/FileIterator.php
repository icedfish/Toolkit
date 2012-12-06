<?php
//yubing@baixing.com

/*
 * Version: v1.0
 *
 * Tested on: php 5.4.9
 *
 * 递归遍历指定目录，查找指定文件后缀的文件
 * 遍历返回的都是DirectoryIterator对象
 *
 * Sample：
 *
 * $it = new FileIterator(ROOT.'/static', 'js');
 * foreach($it as $_js) {
 * 	  echo $_js->getFilename() . "\n";
 * }
 *
 *
 * [ Notice ]
 * 如果不需要递归遍历，只遍历一个目录的话，推荐用更简单的 glob() 方法：
 *
 * $it = glob("/home/haojing/htdocs/lib/*.php");
 * foreach ($it as $_php) {
 * 	echo $_php."\n";
 * }
*/

class FileIterator implements Iterator {
	private $file_ext;
	private $stack = [];

	public function __construct($path, $file_ext = 'php') {
		if (!file_exists($path)) throw new Exception("path: '$path'' not exist!");
		$this->file_ext = $file_ext;
		$this->push($path);
	}

	private function handler() {
		return end($this->stack);
	}

	private function push($path) {
		array_push($this->stack, new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS));
	}

	public function rewind() {
	}

	public function valid() {
		if (!$this->handler()->valid()) {
			if (count($this->stack) > 1) {
				array_pop($this->stack);
				return $this->valid();
			} else {
				return false;
			}
		} else {
			$_item = $this->handler()->current();
			if ($_item->getExtension() == $this->file_ext) {
				return true;
			} elseif ($_item->isDir()) {
				$this->next();
				$this->push($_item->getPathname());
				return $this->valid();
			} else {
				$this->next();
				return $this->valid();
			}
		}
	}

	public function current() {
		return $this->handler()->current();
	}

	public function next() {
		$this->handler()->next();
	}

	public function key() {
		return $this->handler()->key();
	}


}
