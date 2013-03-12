<?php
/*
本程序用于爆破计算Android的锁屏程序的最长的锁屏路径
v1 @2013-03-11  MBA上大约需要跑1800s
*/
class PathFinder {

	private static $pos = [
		//num => [x, y]
		1 => [1,1],
		2 => [1,2],
		3 => [1,3],
		4 => [2,1],
		5 => [2,2],
		6 => [2,3],
		7 => [3,1],
		8 => [3,2],
		9 => [3,3],
	];

	private static $point_cache = [];
	private function findPointOnLine($from = 1, $to = 2) {
		$key = "$from->$to";
		if (!isset(self::$point_cache[$key])) {
			foreach(self::$pos as $_num => $_xy) {
				if (
				$_num != $from && $_num != $to 
				&& ( $this->calDistance($from, $to) == $this->calDistance($from, $_num) + $this->calDistance($_num, $to) )
				) {
					//如果换4x4的话，这里得调整下，可能包含多个点。
					self::$point_cache[$key] = $_num;
					break;
				}
				self::$point_cache[$key] = null;
			}
		}
		return self::$point_cache[$key];
	}
	
	private static $dis_cache = [];
	private function calDistance($from = 1, $to = 2) {
		$x = self::$pos[$from][0] - self::$pos[$to][0];
		$y = self::$pos[$from][1] - self::$pos[$to][1];
		$key = "{$x}->{$y}";
		if(!isset(self::$dis_cache[$key])) {
			self::$dis_cache[$key] = round(hypot(abs($x), abs($y)), 2); //近似计算
		}
		return self::$dis_cache[$key];
	}

	private $find = [
		1 => false,
		2 => false,
		3 => false,
		4 => false,
		5 => false,
		6 => false,
		7 => false,
		8 => false,
		9 => false,
	];

	private function connectPoints($from = 1, $to = 2) {
		$this->find[$from] = true;
		if ($this->find[$to]) {
			return 0;
		} else {
		}
	}

	public function calculate($list = []) {
		$len = 0;
		$now = array_shift($list);
		$this->find[$now] = true;
		foreach ($list as $next) {
			if ($this->find[$next]) continue;

			if ($middle = $this->findPointOnLine($now, $next)) {
				$this->find[$middle] = true;
			}
			$this->find[$next] = true;
			$len += $this->calDistance($now, $next);
			//echo "$now => $next = $len\n";
			$now = $next;
		}
		return $len;
	}
}

$max = [];
$max_num = 0;

$start = time();
//可怜的我发现自己不会写排列组合的代码 >_< ，下一版本改进
for($path_num = 123456789 ; $path_num <= 598764321; $path_num++) {
	$str = strval($path_num);
	if($str[0] == 3) {
		$path_num = 512346789; //3,4和1,2等效
	}
	
	if(strpos($str, '0')) continue;
	
	$path = array_filter(preg_split("//", $str));
	if(count(array_unique($path)) != count($path)) continue;
	$o = new PathFinder();
	$total = $o->calculate($path);
	if ($total > $max_num) {
		$max_num = $total;
		$max = $path;
	}
}

var_dump($max, $max_num, time() - $start);
