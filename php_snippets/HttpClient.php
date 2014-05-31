<?php
//yubing@baixing.com

class HttpClient {
	/**
	 * Get the return of $url through HTTP GET Request
	 *
	 * @param  string    $url     target url
	 * @param  int|float $timeout 单位是秒，1秒以内的用小数表示
	 * @param  array     $opt
	 *
	 * @return  mixed     result
	 */
	public static function get($url, $timeout = 1, $opt = []) {
		$opts = [
				CURLOPT_HTTPGET => true,
			] + $opt;
		return self::request($url, $opts, $timeout);
	}

	/**
	 * Get the return of $url through HTTP POST Request
	 *
	 * @param  string       $url       target url
	 * @param  string|array $post_data data
	 * @param  array        $opt
	 * @param  int|float    $timeout   单位是秒，1秒以内的用小数表示
	 *
	 * @return  mixed        result
	 */
	public static function post($url, $post_data, $timeout = 1, $opt = []) {
		$opts = [
				CURLOPT_POST       => true,
				CURLOPT_POSTFIELDS => $post_data,
			] + $opt;
		return self::request($url, $opts, $timeout);
	}

	/**
	 * 返回是哪个函数调用了当前方法
	 * @return string
	 */
	private static function get_called_function() {
		return array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 2, 1)[0]['function'];
	}

	protected static function request($url, $opts = [], $timeout) {
		$ch = self::init($url, self::get_called_function());

		// Begin: set options
		$opts += [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_ENCODING => '',	//enable gzip
			CURLOPT_URL => $url,
		];
		if ($timeout < 1) {
			throw new Exception("timeout < 1 is not supported yet"); //不止版本要够，还需要手动编译服务器上的libcurl才行
			$opts[CURLOPT_TIMEOUT_MS] = $opts[CURLOPT_CONNECTTIMEOUT_MS] = intval($timeout * 1000);
		} else {
			$opts[CURLOPT_TIMEOUT] = $opts[CURLOPT_CONNECTTIMEOUT] = intval($timeout);
		}
		curl_setopt_array($ch, $opts);
		// End: set options

		$result = curl_exec($ch);
		return curl_errno($ch) ? false : $result;
	}

	private static $pool = [];
	private static function init($url, $function_name){
		if (!preg_match('/^(http[s]?:\/\/[^\/]+\/)/i', $url, $match)) {
			throw new Exception('Only Http(s) Protocol supported! Url: ' . $url);
		}
		/*
		 * Function + Protocol + 域名 + 端口作为key，最大可能地复用TCP连接。
		 * 不同的HTTP请求类型不要公用curl实例，因为一些设置参数不一致，会导致请求类型之间的相互穿越
		 * */
		$key = $function_name . '|' . $match[1];

		if(!isset(self::$pool[$key])) {
			//实例过多的时候回收一次
			if(count(self::$pool) > 100) {
				foreach(self::$pool as $_ch) {
					curl_close($_ch);
				}
				self::$pool = [];
			}
			//新建curl session
			self::$pool[$key] = curl_init();
		}
		return self::$pool[$key];
	}

}
