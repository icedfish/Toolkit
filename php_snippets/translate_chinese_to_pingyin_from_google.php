<?php

/**
 * @Author： YuBing @2014-5-23
 * 常见的直接翻译的类库都太暴力了，多音词很难识别，找了一圈，发现还是Google翻译靠谱，就用他了！
 *
 * @param $chinese_text
 * @param bool $use_origin 返回带声调的标准拼音
 * @return string
 * @throws Exception
 */
function pinyin($chinese_text, $use_origin = false) {
	$target_url = 'http://translate.google.cn/translate_a/t?client=t&sl=zh-CN&tl=zh-CN&hl=en&sc=2&ie=UTF-8&oe=UTF-8&prev=btn&rom=1&srcrom=1&ssel=4&tsel=3&q=';

	$rt = file_get_contents($target_url . urlencode($chinese_text) . '&time=' . time());

	if (mb_strpos($rt, '[[[') !== 0) {
		throw new Exception("Fail to get data from google");
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
		
		//see: http://zh.wikipedia.org/w/index.php?title=%E9%9F%B5%E6%AF%8D&variant=zh-cn
		$yunmu = [
			'a', 'o', 'e', 'i', 'u', 'v',
			'er', 'ai', 'ei', 'ao', 'ou',
			'an', 'ang', 'en', 'eng', 'in', 'ing',  
			'ia', 'io', 'ie', 'ua', 'uo', 've',
			'iai', 'iao', 'iou', 'ian', 'iang', 'iong',
			'uai', 'uei', 'uan', 'uen', 'uang', 'ueng', 'ong',
			'un','van', 'ue',
		];

		$arr_yunmu = [];
		foreach($yunmu as $_ym) {
			$arr_yunmu[$_ym] = "{$_ym} ";
		}
		//拆分连词，比如shíjiān，最好返回 "shi jian", google返回的是 "shijian"
		//@todo 现在这种实现还是有问题，"不能" 会返回 "bun eng" 而不是 "bu neng".
		$pinyin = str_replace("  ", " ", strtr($pinyin, $arr_yunmu));
		
		return $pinyin;
	}

}

// sample: 
//echo pinyin('四阿哥 长大需要长很长时间');
//echo pinyin('iphone');
//echo pinyin('我有一个美国朋友，他想学汉语，可是一点基础都没有，必须从拼音开始学起，由于我们现在都没有时间，也不能经常见面，所以我现在没办法直接教他，希望有人能帮我提供些好的素材，尤其是拼音发声练习方面的，可以暂时供他自己模仿练习用，我从土豆网也下载过类似视频，可是都不完整，而且声音很不清楚，所以希望大家能给我提供些效果很好的材料，谢谢大家');
