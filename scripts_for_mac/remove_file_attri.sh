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
# Known Issue: 文件名带空格等特殊字符会导致找不到文件出错
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
	attris=`xattr -l $file | awk '{print $1}' | grep -v -E '^[0-9a-f]{8}' | sed "s/:$//"`;

	#solution 2, have bugs with some strage attris
	#			like: com.apple.metadata:kMDItemWhereFroms
	#attris=`ls -l@ $file | grep -E '^\s' | awk '{print $1}'`;

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

