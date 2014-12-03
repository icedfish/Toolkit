<?php
//yubing@baixing.com

class GhostChars {
	//三个零宽字符，在浏览器中大多不可见
	const CHAR_1 = '%E2%80%8B'; // '\u200b'
	const CHAR_2 = '%E2%80%8C'; // '\u200c'
	const CHAR_3 = '%E2%80%8D'; // '\u200d'

	static function gen($ch = 'web') {
		$mapping = [
			'web' => urldecode(self::CHAR_1 . self::CHAR_2 . self::CHAR_2),
			'wap' => urldecode(self::CHAR_1 . self::CHAR_2 . self::CHAR_3),
			'app' => urldecode(self::CHAR_1 . self::CHAR_3 . self::CHAR_3),
		];
		return $mapping[$ch];
	}
}

