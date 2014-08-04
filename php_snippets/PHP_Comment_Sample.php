<?php
//yubing@baixing.com

//Definition Sample:

/**
 * Class Category
 * @property String id 类目的Global ID
 * @property Array status 状态，1：正常，2：已删除
 * @property mixed created_time 创建时间点，Unix Timestamp格式
 */
class Category {
	/**
	 * @return string
	 */
	public function type() {
		return 'Category';
	}

	/**
	 * @return Category
	 */
	public function copy() {
		return clone $this;
	}

	/**
	 * @param Category $target
	 * @param array $opt
	 * @return bool
	 */
	public function compare(Category $target, Array $opt = []) {
		return $this->id == $target->id;
	}

}



//Usage Sample:

foreach ($category_array as $_category) {
	/* @var Category $_category */

	$_category->created_time = time();
	$_category->copy()->created_time;
}


// 常容易弄混适合看自动提示的情况 haystack vs needle

// in_array(mixed needle, array haystack) , array_search()
// strpos(string haystack, mixed needle) , strstr() 





