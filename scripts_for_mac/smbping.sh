#!/bin/bash

# smbping.sh
# Written by Damien Sorresso
# 17 August, 2006
# This script can basically be used as a replacement for `ping'
# by using an alias. Here's how it works.
# If $1 contains dots, it will simply pass it through to the normal `ping'. 
# If $1 does not contain dots, it assumes that $1 is a NetBIOS name and 

# Updated by YuBing @ Oct. 2012
# fix bug when script runs under Mountain Lion

# uses `smbutil' to look up the IP of the server.
if [ "$1" = "" ]; then
	echo "Please enter a server name"
	exit 1
fi

# Assume that any server entered without a dot in there somewhere is a NetBIOS name.
IS_NETBIOS=`echo $1 | grep '\.'`

if [ ! "$IS_NETBIOS" = "" ]; then
	ping $1
else
	SERVER=`echo $1 | tr "[a-z]" "[A-Z]"` # Convert argument to all upper-case.
	IP=`smbutil lookup $SERVER | grep IP | awk '{print $NF}'`
	ping $IP

fi
