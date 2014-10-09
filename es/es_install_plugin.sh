##安装ES常用插件脚本
cd /usr/share/elasticsearch/

bin/plugin --install mobz/elasticsearch-head
bin/plugin --install lukas-vlcek/bigdesk
bin/plugin --install royrusso/elasticsearch-HQ
bin/plugin --install lmenezes/elasticsearch-kopf

bin/plugin -i elasticsearch/elasticsearch-repository-hdfs/2.0.1