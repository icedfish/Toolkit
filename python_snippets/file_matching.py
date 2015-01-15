# -*- coding: UTF-8 -*-
import fnmatch
import os

folder = '/Users/yubing/source/XooX/server_list'
str = 'sample*'

for file in os.listdir(folder):
    if fnmatch.fnmatch(file, str):
			print(file)
			fo = open(folder + '/' + file, 'r')
			while True :
				line = fo.readline().strip()
				if not line:
					break
				print "Line: " , line
