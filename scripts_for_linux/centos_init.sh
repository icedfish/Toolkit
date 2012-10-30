#!/bin/sh

echo "blacklist be2iscsi" >> /etc/modprobe.d/blacklist.conf
echo "net.ipv6.conf.all.disable_ipv6 = 1" >> /etc/sysctl.conf

echo '###### Updating System Clock.... ##########'

service ntpd stop
ntpdate 0.pool.ntp.org
service ntpd start

echo '###### Updating Yum.... #######'

yum clean all
yum update -y

echo '###### Set Service Autoruns For INIT 3 #######'
chkconfig --list | grep -E '3:(启用|on)' | awk '{print $1}' | xargs -I {} chkconfig --level 3 {} off  

SERVICES="crond ntpd snmpd network nscd sshd rsyslog"
for ITEM in $SERVICES
do
	chkconfig --level 3 $ITEM on
done

echo '###### Optimize Shell #######'

#简化命令输入
echo "
alias view='vim -R'
alias vi='vim'
alias l='ls -al --color'" >> /etc/bashrc
#alias grep='grep --color'


#彩色提示符
echo "
if [[ \${EUID} == 0 ]] ; then
	PS1='\[\033[01;31m\]\h\[\033[01;34m\] \W \\$\[\033[00m\] '
else
	PS1='\[\033[01;32m\]\u@\h\[\033[01;34m\] \w \\$\[\033[00m\] '
fi" >> /etc/bashrc

#vim配色
echo "
colo ron
syntax on
set tabstop=4" >> ~/.vimrc

echo '###### Add rpmforge and install htop #######'

cd /opt/
wget http://packages.sw.be/rpmforge-release/rpmforge-release-0.5.2-2.el6.rf.x86_64.rpm
rpm -ivh rpmforge-release-0.5.2-2.el6.rf.x86_64.rpm
rpm --import http://apt.sw.be/RPM-GPG-KEY.dag.txt
yum install htop -y



#
# crontab -e 
# Add GLobal Path For Crontab
# PATH=/home/php/bin:/home/www/bin:/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
#

