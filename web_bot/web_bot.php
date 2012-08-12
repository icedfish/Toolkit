<?php

/**
* Author:	YuBinng
* Email:	icedfish@gmail.com
*/

class WebBot {
	private $ch = null;	//CURL Instance

	private $proxy_list = array();


//	@todo 确认curl是否默认传递cookie
	
	public function __construct($timeout = 1, $enable_proxy = false) {
		$this->init_curl_instance(intval($timeout));
		if ( $enable_proxy ) {
			$this->get_valid_proxy_list();
			$this->switch_proxy();
		}
	}

	private function get_valid_proxy_list() {

		$rss = file_get_contents('http://www.sooip.cn/e/web/?type=rss2&classid=0');
		preg_match_all("/<guid>(http:\/\/.+\.html)<\/guid>/", $rss, $urlList);

		foreach ($urlList[1] as $url) {
			preg_match_all("/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\s+(\d{1,4})\s+(HTTP)/i", file_get_contents($url), $matches);
			for ($i = 0; $i < count($matches[0]); $i++) {
				$_proxy = array(
						//暂时这个网站没有Socks代理提供，所有返回都是HTTP
						CURLOPT_PROXYTYPE => ($matches[3][$i] == 'HTTP' ? CURLPROXY_HTTP : CURLPROXY_SOCKS5),
						CURLOPT_PROXY => $matches[1][$i],
						CURLOPT_PROXYPORT => $matches[2][$i],
					);

				//开始校验proxy是否有效
				$ch_for_validate = curl_init();
				curl_setopt_array($ch_for_validate, array(
						CURLOPT_TIMEOUT => 2,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_URL => 'http://www.taobao.com/robots.txt',
						)
				);
				curl_setopt_array($ch_for_validate, $_proxy);
				$result = curl_exec($ch_for_validate);
				$code = curl_errno($ch_for_validate);
				curl_close($ch_for_validate);

				if ( $code == 0 && strpos($result, 'User-agent') !== false) {
					$this->proxy_list[$matches[1][$i]] = $_proxy;
					echo "Proxy @{$matches[1][$i]} verified\n";
				}
				if(count($this->proxy_list) == 1000 ) {
					return;	//上限用1000个proxy
				}
			}
		}
	}

	private function init_curl_instance( $timeout ) {
		$this->ch = curl_init();
		curl_setopt_array($this->ch, array(
				CURLOPT_TIMEOUT => $timeout,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,	//支持抓取302/301跳转后的页面内容
			)
		);
	}

	public function switch_proxy() {
		if(empty($this->proxy_list)) throw new Exception('没有代理可以用了！');
		//curl_close($this->ch); 可以直接close么？
		curl_setopt_array($this->ch, array_pop($this->proxy_list));		
		echo "Proxy: " . count($this->proxy_list) . " selected\n";
	}

	private function request() {
		$result = curl_exec($this->ch);
		return (curl_errno($this->ch) == 0) ? $result : false;
	}

	public function do_post($url, $params=array()) {
		curl_setopt_array($this->ch, array(
				CURLOPT_URL => $url,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $params,
			)
		);
		return $this->request();
	}

	public function do_get($url) {
		curl_setopt($this->ch, CURLOPT_URL, $url);
		curl_setopt($this->ch, CURLOPT_REFERER, $url);	//最后访问的URL，用于后续请求的Referer参数
		return $this->request();
	}


	
}


/*------------Sample:--------------*/

echo "\nStart to vote:\n\n";

$bot = new WebBot(5, true);

while(true) {
	//$bot->do_get('http://www.cxzsw.org/index.php/people/detail/57'); //伪装客户端访问一下
	echo "\n" . $bot->do_post(
			'http://www.cxzsw.org/index.php/people/voteing/57', 
			array('views' => '1')
		);
	try {
		$bot->switch_proxy();
	} catch (Exception $e) {
		break;
	}
	sleep(rand(2,10));
}

echo "\ndone!";


?>