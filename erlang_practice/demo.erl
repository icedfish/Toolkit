-module(translate).
-export([loop/0]).

loop() ->
    receive
            "dong" ->
                        io:format("house~n" ),
                        loop();
            "blanca" ->
                        io:format("white~n" ),
                        loop();        
            _ ->            
            			io:format("I don't understand.~n" ),
            			loop()
end.