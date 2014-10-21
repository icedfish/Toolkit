from multiprocessing import Process
import time  
from setproctitle import *

# http://outofmemory.cn/code-snippet/2267/Python-many-process-multiprocessing-usage-example
# https://code.google.com/p/py-setproctitle/

# @tested:
#	 works on ubuntu 14.04
#	 not works on mac osx 10.10


def takeuptime(n):
    setproctitle('subprocess for ' + str(n))
    chars = 'abcdefghijklmnopqrstuvwxyz0123456789'  
    s = chars * 1000  
    for i in range(10*n):  
        for c in chars:  
            s.count(c)  

# Multiprocessing computation  
list_of_args = [1001, 1002, 1003, 1004]  

process = []  
nprocess = 4 # number of processes  
for i in range(nprocess):  
    process.append(Process(target=takeuptime, args=(list_of_args[i],)))  
start = time.time()  
# Start processes one by one  
for p in process:  
    p.start()

# Wait for all processed to finish  
for i in process:  
    i.join()  
print "%f s for multiprocessing computation." % (time.time() - start)  