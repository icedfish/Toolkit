-module(hello).
-export([say/1]).

say(Anything) -> private_say(Anything).

% 没有export的方法，是module内的私有方法。
private_say(Sth) -> Sth.

% 列表处理语法糖
%  [{Item, Quantity * Price} || {Item, Quantity, Price} <- [{ book, 31, 4.5 }, { food, 50, 1 }]].
