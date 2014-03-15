-module(math).  %往现有的math module上增加新的函数。
-export([fib/1, factorial/1]).
-export([fib_plus/1, factorial_plus/1]).

fib(N) when (N =< 0) orelse (not is_integer(N)) -> 'error input!' ;
fib(N) when N > 2 -> fib(N-2) + fib(N-1);
fib(2) -> 1 ;
fib(1) -> 1 .


factorial(N) when (N =< 0) orelse (not is_integer(N)) -> 'error input!' ;
factorial(1) -> 1 ;
factorial(N) -> factorial(N-1) * N.
%最后这两行倒换效率是否有差异？ 
%Guard计算效率高还是Clause比对效率高？
%第一行在大量计算时候是否降低效率？ 
%这些都需要继续深入研究。

%@todo 上述两个函数都需要做尾递归优化




%%%%%%%%%%%%%%%%%%%%%%%%% 效率的分割线 %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
%相关参考： http://www.erlang.org/doc/reference_manual/functions.html


%独立的错误检查，提高循环体执行效率，减少不必要的重复判断。
fib_plus(N) when (N =< 0) orelse (not is_integer(N)) -> 'error input!' ;
fib_plus(1) -> 1;
fib_plus(2) -> 1;
fib_plus(N) when N > 2 -> fib_tail_optimized(N, 3, fib_plus(1), fib_plus(2)).

%采用了尾递归优化，计算复杂度提升很多，而且编译器自动的尾递归优化，还可以节省客观的栈空间。
% @todo，刚接触尾递归的概念，想出来的写法自我感觉有点丑，以后再想想还有没有更好的写法。

fib_tail_optimized(Target_num, Now_num, N_2, N_1) ->
	if
		Target_num == Now_num ->
			N_1 + N_2;
		true ->
			fib_tail_optimized(Target_num, Now_num+1, N_1, N_1 + N_2)
	end.

%阶乘的优化ms几乎没有效果，应该是自动触发了尾递归优化，而且下面这行在实际计算中占比太小，表现不出差距。
factorial_plus(N) when (N =< 0) orelse (not is_integer(N)) -> 'error input!' ;
factorial_plus(N) -> factorial_tail_optimized(N-1, N).

factorial_tail_optimized(Now_num, Total) -> 
	if
		Now_num == 1 ->
			Total;
		true ->
			factorial_tail_optimized(Now_num - 1, Now_num * Total)
	end.
