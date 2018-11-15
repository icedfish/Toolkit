<?php
//Get list form https://github.com/harley84/ZeroLengthRadar/blob/master/src/main/java/com/ultrahob/zerolength/InvisibleCharacterInspection.java

class ZeroLengthChars
{
	const CHAR_LIST = [
	        '\u200b' => 'Zero width space',
	        '\u200c' => 'Zero width non-joiner',
	        '\u200d' => 'Zero width joiner',
	        '\u200e' => 'Left to right mark',
	        '\u200f' => 'Right to left mark',
	        '\ufeff' => 'Zero width no-break space',
	        '\u2028' => 'Line separator',
	        '\u0003' => 'End of Text character',
            //'\u00a0' => 'No-Break space character', //这个浏览器下看到是空格,不是零宽
    ];
	
	public static function list()
	    {
		$list = [];
		foreach (self::CHAR_LIST as $k => $v) {
			$list[$v] = json_decode('"' . $k . '"');
		}
		return $list;
	}
}

var_dump(ZeroLengthChars::list());
die('here');
