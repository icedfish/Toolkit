#!/bin/bash

#########################################################
# Author:	YuBing <icedfish@gmail.com>
#
# Usage:
#		1. Single File:
#			bash this_script file_dir
#		2. A Whole Folder（非递归）:
#			ls -l@ folder_dir | grep rw | grep '@ ' |awk '{print $NF}' | bash this_script
#
# Note:		用于清除Mac系统给文件上面加的各种多余属性
#			常见两种问题：
#				1. 打开软件的时候提示应用程序是互联网下载的，可能不安全
#					每次打开需要点击确认。
#				2. NTFS的移动硬盘上有些文件在mac下面显示灰色，无法打开。
# 			http://en.wikipedia.org/wiki/Extended_file_attributes
#
# Version:	0.3
# Last Modify: 2012-8-12
# Tested: Mac OS X - Mountain Lion
#########################################################


function cleanup_single_target {
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
}

#处理单个输入
if [ ! -z $1 ]; then
	cleanup_single_target $1;
	exit;
fi

#处理管道输入
while read line; do
	cleanup_single_target $line;
done

