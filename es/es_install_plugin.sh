##安装ES常用插件脚本
cd /usr/share/elasticsearch/  #for linux yum installed
cd /usr/local/Cellar/elasticsearch/2.3.4/libexec/  #for mac brew installed

bin/plugin install mobz/elasticsearch-head
bin/plugin install lmenezes/elasticsearch-kopf
bin/plugin install royrusso/elasticsearch-HQ

# install IK
ver="1.9.4"
mkdir plugins/ik
cd plugins/ik
wget https://github.com/medcl/elasticsearch-analysis-ik/releases/download/v$ver/elasticsearch-analysis-ik-$ver.zip
unzip elasticsearch-analysis-ik-$ver.zip
rm -f elasticsearch-analysis-ik-$ver.zip


# Mavel Agent
bin/plugin install license
bin/plugin install marvel-agent

#install kibana first
/opt/kibana/bin/kibana plugin --install elasticsearch/marvel/latest