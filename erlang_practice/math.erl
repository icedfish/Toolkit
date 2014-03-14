-module(math).  %往现有的math module上增加新的函数。
-export([fib/1, factorial/1]).

fib(N) when (N < 0) orelse (not is_integer(N)) -> 'error input!' ;
fib(0) -> 0 ;
fib(1) -> 1 ;
fib(N) when N > 1 -> fib(N-2) + fib(N-1).


factorial(N) when (N <= 0) orelse (not is_integer(N)) -> 'error input!' ;
factorial(1) -> 1 ;
factorial(N) -> factorial(N-1) * N.

%@todo 两个函数都需要做尾递归优化