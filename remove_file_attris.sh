#!/bin/bash

#########################################################
# Author:	YuBing <icedfish@gmail.com>
# Usage:	bash this_script target
# Note:		用于清除Mac系统给文件上面加的各种多余属性
# http://en.wikipedia.org/wiki/Extended_file_attributes
#########################################################

file=$1;

if [ ! -e "$file" ];then
	echo "$file is not an exsting file";
	exit 1;
fi

#best solution 
attris=`ls -Rl@ $file | grep -E '^\s' | awk '{print $1}'`;
#attris=$(ls -Rl@ $file | grep -E '^\s' | awk '{print $1}');


#solution 2, have bugs when there is hex outputs
#attris=`xattr -l $file | awk '{print $1}' | sed "s/:$//"`;

for i in $attris;
do
	echo "Remove $i from $file";
	xattr -d $i $file;
done
