
from: http://www.elasticsearch.org/guide/en/elasticsearch/reference/0.90/modules-jmx.html

## @elasticsearch.yml

```
jmx.create_connector: true
```

## @elasticsearch.in.sh


# ensures JMX accessible from outside world

```
JAVA_OPTS="$JAVA_OPTS -Dcom.sun.management.jmxremote.ssl=false"
JAVA_OPTS="$JAVA_OPTS -Dcom.sun.management.jmxremote.authenticate=false"
JAVA_OPTS="$JAVA_OPTS -Djava.rmi.server.hostname=$(hostname -i)"
```

如果启动时候出现 java.net.MalformedURLException: Local host name unknown:xxx ,那么确保hostname可以解析到这个ip。
