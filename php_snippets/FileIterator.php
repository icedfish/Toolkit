<?php
//yubing@baixing.com

/*
 * Version: v1.1
 *
 * Tested on: php 5.4.9 & 5.3.10
 *
 * 递归遍历指定目录，查找指定文件后缀的文件
 * 遍历返回的都是DirectoryIterator对象
 *
 * Sample：
 *
 * $it = FileIterator::create(ROOT.'/static', 'js');
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

class FileIterator {
	public static function create($path, $file_ext = 'php') {
		if (!file_exists($path)) throw new Exception("path: '$path'' not exist!");
		$handler = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
		return new ExtensionFilter($handler, $file_ext);
	}
}

class ExtensionFilter extends FilterIterator {
	private $file_ext;

	public function __construct(Iterator $iterator, $file_ext = 'php') {
		parent::__construct($iterator);
		$this->file_ext = $file_ext;
	}

	public function accept() {
		$item = $this->getInnerIterator()->current();
		return $item->getExtension() == $this->file_ext;
	}
}
