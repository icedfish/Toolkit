#!/bin/sh

#脚本解释: 
#从网站下载广告网址并转义保存到/etc/dnsmasq.ads
#dnsmasq拦截将广告网址IP定义为127.0.0.1地址
#从dnsmasq.conf配置文件中引用/etc/dnsmasq.ads
#重启dnsmasq
#脚本添加执行权限

# crontab:
#0 0 * * * sh /etc/update_dnsmasq.sh

wget -O - http://adblock-chinalist.googlecode.com/svn/trunk/adblock-lazy.txt | grep ^\|\|[^\*]*\^$ | sed -e 's:||:address\=\/:' -e 's:\^:/127\.0\.0\.1:' > /etc/dnsmasq.ads

grep conf-file /etc/dnsmasq.conf || echo -e "\nconf-file=/etc/dnsmasq.ads" >> /etc/dnsmasq.conf

/etc/init.d/dnsmasq restart
