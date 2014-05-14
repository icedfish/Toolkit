-module(translate).
-export([loop/0]).

loop() ->
    receive
            "中国" ->
                        io:format("China~n" ),
                        loop();
            "日本" ->
                        io:format("Japan~n" ),
                        loop();        
            _ ->            
            			io:format("I don't understand.~n" ),
            			loop()
end.