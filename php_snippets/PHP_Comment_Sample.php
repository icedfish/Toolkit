<?php

//Definition Sample:

/**
 * Class Category
 * @property mixed id 类目的Global ID
 * @property mixed status 状态，1：正常，2：已删除
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
		return new Category;
	}
}

//Usage Sample:

foreach ($category_array as $_category) {
	/* @var Category $_category */

	$_category->created_time = time();

	$_category->copy()->copy();
}

