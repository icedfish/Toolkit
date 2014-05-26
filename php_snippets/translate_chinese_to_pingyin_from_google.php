<?php

/**
 * @Author： YuBing  updated @2014-5-26
 * 常见的直接翻译的类库都太暴力了，多音词很难识别，找了一圈，发现还是Google翻译靠谱，就用他了！
 * 这个方案适合翻译精确度要求较高，但是请求频率较低的情况，否则容易被Google防火墙Ban掉。
 *
 * @param $chinese_text
 * @param bool $use_origin 返回带声调的标准拼音
 * @return string
 * @throws Exception
 */
function pinyin($chinese_text, $use_origin = false) {
	$chinese_text = str_replace(['"', "'"], ' ', $chinese_text); //去掉特殊符号，避免后面判断错误。
	
	$target_url = 'http://translate.google.cn/translate_a/t?client=t&sl=zh-CN&tl=zh-CN&hl=en&sc=2&ie=UTF-8&oe=UTF-8&prev=btn&rom=1&srcrom=1&ssel=4&tsel=3&q=';

	$rt = file_get_contents($target_url . urlencode($chinese_text) . '&time=' . time());

	if (mb_strpos($rt, '[[[') !== 0) {
		throw new Exception("Fail to get data from google. Response: {$rt}");
	}

	/*
		Sample Response:
		$rt = '[[["四阿哥 长大需要长很长时间","四阿哥 长大需要长很长时间","Sì'ā gē zhǎng dà xūyào zhǎng hěn cháng shíjiān","Sì'ā gē zhǎng dà xūyào zhǎng hěn cháng shíjiān"]],,"zh-CN",,[["四阿哥 长大需要长很长时间",,false,false,0,0,0,0]],,,,[["zh-CN","zh-CN"]],4]';
	*/

	preg_match("/^\[\[\[([^\]]+)\]/", $rt, $matches);
	$pinyin = mb_strtolower(explode('","', $matches[1])[2]);

	if ($use_origin) {
		return $pinyin;
	} else {
		
		//去掉音调，转为标准英文写法。
		$replace = [
			'ā' => 'a', 'á' => 'a', 'ǎ' => 'a', 'à' => 'a',
			'ō' => 'o', 'ó' => 'o', 'ǒ' => 'o', 'ò' => 'o',
			'ē' => 'e', 'é' => 'e', 'ě' => 'e', 'è' => 'e',
			'ī' => 'i', 'í' => 'i', 'ǐ' => 'i', 'ì' => 'i',
			'ū' => 'u', 'ú' => 'u', 'ǔ' => 'u', 'ù' => 'u',
			'ǖ' => 'v', 'ǘ' => 'v', 'ǚ' => 'v', 'ǜ' => 'v',
		];
		$pinyin = str_replace("'", " ", $pinyin); // 去掉多余的单引号，比如“四阿哥” => "Sì'ā gē"
		$pinyin = strtr($pinyin, $replace);
		
		//拆分连词，比如shíjiān，最好返回 "shi jian", google返回的是 "shijian"
		//已解决问题： "不能" 返回 "bu neng" 而不是 "bun eng".
		$yunmu = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";

		$arr_yunmu = [];
		foreach(explode('|', $yunmu) as $_ym) {
			$arr_yunmu[$_ym] = "{$_ym} ";
		}
		$pinyin = str_replace("  ", " ", strtr($pinyin, $arr_yunmu));
		
		return $pinyin;
	}

}

// sample: 
//echo pinyin('四阿哥 长大需要长很长时间');
//echo pinyin('iphone');
//echo pinyin('我有一个美国"朋友"，他想学汉语，可是一点基础都没有，必须从拼音开始学起，由于我们现在都没有时间，也不能经常见面，所以我现在没办法直接教他，希望有人能帮我提供些好的素材，尤其是拼音发声练习方面的，可以暂时供他自己模仿练习用，我从土豆网也下载过类似视频，可是都不完整，而且声音很不清楚，所以希望大家能给我提供些效果很好的材料，谢谢大家');
