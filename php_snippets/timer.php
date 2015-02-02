<?php
//小工具，方便做benchmark。


function timer(callable $func, $repeats = 1) {
	$start = microtime(true);
	while ($repeats > 0) {
		call_user_func($func);
		$repeats--;
	}
	$time = (microtime(true) - $start);
	echo $time * 1000 . ' ms';
}

$a = function () {
	$a = 1;
	$a++; 
};

timer($a,300);