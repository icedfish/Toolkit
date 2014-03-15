-module(math).  %往现有的math module上增加新的函数。
-export([fib/1, factorial/1]).

fib(N) when (N =< 0) orelse (not is_integer(N)) -> 'error input!' ;
fib(N) when N > 1 -> fib(N-2) + fib(N-1);
fib(1) -> 1 .


factorial(N) when (N =< 0) orelse (not is_integer(N)) -> 'error input!' ;
factorial(1) -> 1 ;
factorial(N) -> factorial(N-1) * N.
%最后这两行倒换效率是否有差异？ Guard计算效率高还是Clause比对效率高？第一行在大量计算时候是否降低效率？ 这些都需要继续深入研究。

%@todo 两个函数都需要做尾递归优化