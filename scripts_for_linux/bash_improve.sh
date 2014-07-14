# for ubuntu bash #
# attach to the end of /etc/bash.bashrc


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