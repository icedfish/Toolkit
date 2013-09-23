<?php

/*
* 记录一些碰到的PHP陷阱，方便以后提醒自己和分享给别人。
*/


/*
  [goto陷阱]
defaut: 会被识别成goto的标志位，所以这个case里面虽然它拼写错误，语法却没有错误，坑爹啊。。。
*/
function foo($var) {
	switch ($var) {
		case 'a';
			return 'aa';
		case 'b';
			goto defaut;
		defaut: //注意，是default拼错了!
			return 'wrong!';
	}
}

var_dump(foo('a'), foo('b'), foo('c'));


/*
 [nl2br()的神设计]
官方手册上实际写明了：
Returns string with '<br />' or '<br>' inserted before all newlines (\r\n, \n\r, \n and \r).
可是一个叫aa2bb的方法，实际表现竟然不是替换而是添加，有够诡异！
*/



