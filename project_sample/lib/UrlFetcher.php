<?php

class UrlFetcher {
	private $ch;
	private $cookie = '';

	/**
	 * @var $use_proxy 会有以下三种状态：
	 *  1. null => 不用代理;
	 *  2. [] => 使用随机代理;
	 *  3. ['host' => xx, 'type' => xx, 'delay' => xx] => 强制指定使用的Proxy
	 */
	private $use_proxy = null;

	const DEFAULT_UA = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)';
	const MOBILE_UA  = 'Mozilla/5.0 (iPhone; CPU iPhone OS 7_0_3 like Mac OS X) AppleWebKit/531.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11B511 Safari/9537.53';

	public function getUrl($url, $timeout = 5) {
		$this->initInstance();
		curl_setopt($this->ch, CURLOPT_URL, $url);
		curl_setopt($this->ch, CURLOPT_TIMEOUT, intval($timeout));

		if ($this->cookie) {
			curl_setopt($this->ch, CURLOPT_COOKIE, $this->cookie);
		}

		if (is_array($this->use_proxy)) {
			$retry = $this->use_proxy ? 1 : 3;
			for ($i = 0; $i < $retry; $i++) {
				$proxy = $this->use_proxy ?: Proxy::rand(); //允许手动指定Proxy配置
				curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $proxy['delay']);
				curl_setopt($this->ch, CURLOPT_PROXY, $proxy['host']);
				curl_setopt($this->ch, CURLOPT_PROXYTYPE, $proxy['type']);
				$result = curl_exec($this->ch);
				if ($result && !curl_errno($this->ch)) break;
			}
		} else {
			$result = curl_exec($this->ch);
		}

		return curl_errno($this->ch) ? false : $result;
	}

    public function getCurlInfo() {
        return curl_getinfo($this->ch);
    }

	private function initInstance() {
		if ($this->ch) return; //因为只开发GET请求，所以可以直接复用CURL实例
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($this->ch, CURLOPT_HTTPGET, true); //默认这个Class只支持GET请求！
		$this->initHeader();
	}

	private function initHeader() {
		$header = array(
			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Accept-Language' => 'zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3',
		);
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_USERAGENT, self::DEFAULT_UA);
		curl_setopt($this->ch, CURLOPT_ENCODING, ""); //all supported encoding types will be sent
	}

	//使用Proxy抓取，具体用哪个Proxy可以随机。
	public function useRandomProxy() {
		$this->setCookie();//随机代理情况一定希望cookie不要跟着的,要不不是让人抓么.
		$this->use_proxy = [];
		return $this;
	}

	//使用给定的Proxy配置
	public function useStaticProxy($proxy = []) {
		$this->use_proxy = $proxy;
		return $this;
	}

	//格式： "fruit=apple; colour=red"， CURL默认会记住服务器返回的Cookie。
	public function setCookie($cookie = 'N=A') { //使用默认参数相当于服务器返回的Cookie都不认。
		$this->cookie = $cookie;
		return $this;
	}

	public function setUserAgent($ua_string) {
		curl_setopt($this->ch, CURLOPT_USERAGENT, $ua_string);
		return $this;
	}

	public function followLocation($max_redirection) {
		$max_redirection = intval($max_redirection);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, ($max_redirection > 0));
		curl_setopt($this->ch, CURLOPT_MAXREDIRS, $max_redirection);
		return $this;
	}

}
