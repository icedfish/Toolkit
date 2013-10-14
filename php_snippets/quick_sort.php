<?
//快速排序（一维数组）
function quick_sort(array $data) {
	$count = count($data);
	if ($count <= 1) return $data;

	$key = $data[0];
	$left_arr = array();
	$right_arr = array();
	for ($i = 1; $i < $count; $i++) {
		if ($data[$i] <= $key) {
			$left_arr[] = $data[$i];
		} else {
			$right_arr[] = $data[$i];
		}

	}
	$left_arr = quick_sort($left_arr);
	$right_arr = quick_sort($right_arr);

	return array_merge($left_arr, array($key), $right_arr);
}


$test_arr = [];

for ($i = 0; $i < 10000; $i++) {
	$test_arr[] = mt_rand(1, 9999999);
}

$sorted_arr = quick_sort($test_arr);
print_r($sorted_arr);
