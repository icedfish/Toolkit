-module(tcp).
-export([start/0, machine_client/1, machine_server/0]).

-define(SEQ_CLIENT, getSeq()).
-define(SEQ_SERVER, getSeq()).
% -define(SEQ_CLIENT, 123).
% -define(SEQ_SERVER, 321).
-define(CLOSE_ACK, 789).

getSeq() ->
 random:seed(erlang:now()),
 random:uniform(erlang:trunc(math:pow(2, 31))).

machine_client(Pack) ->
 server ! Pack,
 machine_client_listen().
 
machine_client_listen() ->
 receive
  {syn, ack, Seq, Ack} ->
   %% 注意：第二次握手时，client会在SYN_SENT和ESTABLISHED之间短暂处于SYN_RCVD状态
   %% 此时，也就是"syn攻击"[1]生效的阶段
   io:format("server[SYN_RCVD] -> client[SYN_SENT/ESTABLISHED], 第二次握手，syn和ack均置为1，Seq = ~w, Ack = ~w~n~n", [Seq, Ack]),
   timer:sleep(2000),
   machine_client({ack, ?SEQ_CLIENT + 1, ?SEQ_SERVER + 1});
  %% 断开连接
  {fin, Seq, Ack} ->
   io:format("server[FIN_WAIT_1] -> client[ESTABLISHED/CLOSE_WAIT], 第一次挥手，fin置为1，Seq = ~w, Ack = ~w~n~n", [Seq, Ack]),
   timer:sleep(2000),
   server ! {ack_fin, ?CLOSE_ACK, ?SEQ_SERVER + 1},
   %% 客户端又忙活了一阵（也可能没有忙活）后，也学服务器端跟他say goodbye一样，
   %% 向服务端发起一个fin报文段宣告终止tcp连接
   timer:sleep(4000),
   %% 注意，客户端发起fin报文段的Seq number和Ack number和最近一次发的ack确认相同
   %% 客户端一旦发起fin报文段，将从CLOSE_WAIT状态变为LAST_ACK状态
   machine_client({fin, ?CLOSE_ACK, ?SEQ_SERVER + 1});
  {ack_fin, Seq, Ack} ->
   io:format("server[TIME_WAIT] -> client[LAST_ACK/CLOSED], 第四次挥手，ack置为1, Seq = ~w, Ack = ~w~n~n", [Seq, Ack])
 end.

machine_server() ->
 receive
  {syn, Seq} ->
   io:format("client[SYN_SENT] -> server[LISTEN/SYN_RCVD], 第一次握手，syn置为1，Seq = ~w~n~n", [Seq]),
   timer:sleep(2000),
   %% 参数顺序参考wireshark抓包数据
   client ! {syn, ack, ?SEQ_SERVER, ?SEQ_CLIENT + 1},
   machine_server();
  {ack, Seq, Ack} ->
   %% 事实上发起第三次握手之前，server会对client过来的seq和ACK进行检验，符合要求才会发起第三次握手
   %% 相应的，client也会在收到server的第三次握手请求之后进行上述检验，一切OK的话，连接最终建立
   io:format("client[ESTABLISHED] -> server[ESTABLISHED], 第三次握手，ack置为1，Seq = ~w, Ack = ~w~n~n", [Seq, Ack]),
   io:format("连接建立~n"),
   timer:sleep(4000),
   io:format("~n~n~n"),
   %% 服务器端首先发起断开连接的请求
   client ! {fin, ?SEQ_SERVER, ?CLOSE_ACK},
   machine_server();
  {ack_fin, Seq, Ack} ->
   io:format("client[CLOSE_WAIT] -> server[FIN_WAI_1/FIN_WAI_2], 第二次挥手，ack置为1，Seq = ~w, Ack = ~w~n~n", [Seq, Ack]),
   %% 服务端接到来自客户端对第一次挥手的确认后，坐等客户端发起断开连接请求
   machine_server();
  {fin, Seq, Ack} ->
   %% 向客户端发起挥手确认后，服务端还有等待2MSL才能由TIME_WAIT态转到CLOSED态
   io:format("client[LAST_ACK] -> server[FIN_WAI_1/TIME_WAIT], 第三次挥手，fin置为1，Seq = ~w, Ack = ~w~n~n", [Seq, Ack]),
   timer:sleep(2000),
   client ! {ack_fin, ?SEQ_SERVER + 1, ?CLOSE_ACK + 1},
   wait2MSL()
 end.
 
%% 假设一个MSL = 3000ms
wait2MSL() ->
 receive
  after 6000 -> ok
 end,
    io:format("连接断开~n").
 
%% 搞起  
start() ->
 register(client, spawn(tcp, machine_client, [{syn, ?SEQ_CLIENT}])),
 register(server, spawn(tcp, machine_server, [])).