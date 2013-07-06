<?php
/*
* PHP在处理特殊情况下的加减运算非常有趣。
*/

	$a = false;
	$a--;
	var_dump($a);
	$a++;
	var_dump($a);

	$a = null;
	$a--;
	var_dump($a);
	$a++;
	var_dump($a);

	$a = 'AA';
	$a--;
	var_dump($a);
	$a++;
	var_dump($a);

	$a = 'Z';
	$a--;
	var_dump($a);
	$a++;
	var_dump($a);

	$a = 'Z';
	$a++;
	var_dump($a);
	$a--;
	var_dump($a);

?>