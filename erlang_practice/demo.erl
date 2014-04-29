-module(demo).
-export([ttt/1]).
% PRACTICE FOR GAME tic-tac-toe
% @todo 如何实现方法内部的常量定义？

%List = [x,o,o, x,o,free, x,x,o].

ttt(List) when any(free, List) -> continue;
ttt(List) -> 
	
good	%任意三个，距离为1的格子内容相同，记为胜，否则为未知胜负
.