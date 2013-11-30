#!/bin/sh

#脚本原理: 
#从adblock-chinalist下载广告网址
#dnsmasq拦截将广告网址IP定义为127.0.0.1地址
#重启dnsmasq

# crontab:
#0 0 * * * /bin/sh /etc/dnsmasq/update_dnsmasq.sh

# v1: use pure dnsmasq format config #
# v1 代码来源于互联网，原始作者未考证#
#wget -O - http://adblock-chinalist.googlecode.com/svn/trunk/adblock-lazy.txt | grep ^\|\|[^\*]*\^$ | sed -e 's:||:address\=\/:' -e 's:\^:/127\.0\.0\.1:' > /etc/dnsmasq.ads
#grep conf-file /etc/dnsmasq.conf || echo -e "\nconf-file=/etc/dnsmasq.ads" >> /etc/dnsmasq.conf
#/etc/init.d/dnsmasq restart

# v2: use hosts like files #
# v2 比v1 ：大幅节省空间占用和效率(路由器的硬件比较弱)
mkdir -p /etc/dnsmasq

wget -O - http://adblock-chinalist.googlecode.com/svn/trunk/adblock-lazy.txt | grep ^\|\|[^\*]*\^$ | grep -v cloudfront.net | grep -v rackcdn.com | sed -e 's#||##' -e 's#\^##'| tr "\n" " " |sed -e "s#^#127.0.0.1 #" > /etc/dnsmasq/disabled_simple_hosts

grep addn-hosts /etc/dnsmasq.conf || echo -e "\naddn-hosts=/etc/dnsmasq/disabled_simple_hosts" >> /etc/dnsmasq.conf

/etc/init.d/dnsmasq reload
