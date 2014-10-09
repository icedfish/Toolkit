# for ubuntu bash #
# /etc/profile.d/improve.sh


if [[ ${EUID} == 0 ]] ; then
        PS1='\[\033[01;31m\]\u@\h\[\033[01;34m\] \W \\$\[\033[00m\] '
else
        PS1='\[\033[01;32m\]\u@\h\[\033[01;34m\] \w \\$\[\033[00m\] '
fi

export LC_CTYPE="en_US.UTF-8"
export LC_MESSAGES="en_US.UTF-8"
export LC_ALL="en_US.UTF-8"

alias 'vi'='vim'
alias 'll'='ls -al --color'
alias 'l'='ls -l'
alias 'grep'='grep --color'

#enable coloring of terminal
export CLICOLOR=1


#####################
### other comfigs ###
#####################

# [1] change ulimit by /etc/security/limits.conf
* soft nofile 65536
* hard nofile 65536

# [2] disable ipv6 by /etc/sysctl.conf
##disable ipv6
net.ipv6.conf.all.disable_ipv6 = 1
net.ipv6.conf.default.disable_ipv6 = 1
net.ipv6.conf.lo.disable_ipv6 = 1
## vi /etc/ssh/sshd_config
ListenAddress 0.0.0.0
