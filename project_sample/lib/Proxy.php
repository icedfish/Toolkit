<?php

class Proxy {
	const MAX_PROXY_DELAY = 5;

	/**
	 *     Proxy类中打代理信息都用数组形式传递，结构说明：
	 *  'host'    => 格式："代理Ip:代理端口"
	 *  'type'    => CURLPROXY_HTTP or CURLPROXY_SOCKS5 代理类型
	 *  'delay'    => 连接到代理自身的平均时延（秒），如果使用此代理，curl超时设的应该至少不小于这个值。
	 */

	/**
	 * 随机返回一个库中的Proxy （注意，有一定概率返回的Proxy是不可用的，这个层面的代码无法保证！）
	 *
	 * @return array 代理信息
	 */
	public static function rand() {
		$list = self::all();
		if (!$list) throw new Exception("Proxy数据文件不存在或者读取出错!");
		$one = $list[array_rand($list)];
		return
			[
				'host' => $one,
				'type' => CURLPROXY_HTTP,
				'delay' => self::MAX_PROXY_DELAY, //先写死，回头再看是否要支持不同proxy记下不同的
			];
	}

	public static function all() {
		return @file(self::dataPath(), FILE_IGNORE_NEW_LINES) ?: [];
	}

	private static function dataPath() {
		return ROOT . '/data/ip.txt';
	}

	/**
	 * @param array $proxy
	 * @return bool
	 */
	private function isValidProxy(array &$proxy) {
		$fetch = new UrlFetcher();
		$proxy['delay'] = self::MAX_PROXY_DELAY;
		$fetch->useStaticProxy($proxy);

		if (!preg_match("#User-agent:#", $fetch->getUrl('http://baidu.com/robots.txt'))) return false;

		$result = trim(substr($fetch->getUrl('http://members.3322.org/dyndns/getip'), 0, 32)); //避免错误时返回过多的HTML
		$info = $fetch->getCurlInfo();
		Logger::log(date('Ymd H:i:s => ') . $proxy['host'] . '  ' . $result . '  ' . $info['total_time']);

		if (!preg_match("#^([0-9\.]{7,})#", $result, $m)) {
			return false;
		} else {
			list($h, $p) = explode(':', $proxy['host']);
			if (!$this->is_the_same_subnet($h, $m[1])) {
				//有时候代理出来的IP不一定严格和代理一样，可能是同网段的。
				return false;
			} else {
				return ($info['total_time'] <= self::MAX_PROXY_DELAY);
			}

		}
	}

	private function is_the_same_subnet($ip_a, $ip_b) {
		$mask = 8; //A段24 B段16 C段8
		return (ip2long($ip_a) >> $mask) == (ip2long($ip_b) >> $mask);
	}

	//@todo 需要换成多线程模式，单线程校验太慢了。
	private function filterProxies($list) {
		$valid = [];
		foreach ($list as $line) {
			if (preg_match("#^[0-9:\.]+$#", $line)) {
				$_config = ['type' => CURLPROXY_HTTP, 'host' => $line];
				if ($this->isValidProxy($_config)) {
					$valid[] = $line;
					Logger::log(count($valid) . " total found!");
				}
			}
		}
		return $valid;
	}

	public function addProxies($list) {
		shuffle($list); //乱序检查，有时候拿到的IP列表会出现一个ip多端口上有代理，应该被打散开查。
		$valid = $this->filterProxies($list);

		$all = array_unique( array_merge($valid + self::all()) );
		Logger::log("Get " . count($valid) . " proxies from " . count($list));
		return file_put_contents(
			self::dataPath(),
			join("\n", $all)
		);
	}

}
