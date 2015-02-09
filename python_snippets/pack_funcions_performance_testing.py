# -*- coding: UTF-8 -*-

import pickle
import cPickle
import json
import zlib
import ujson

import time 
class Timer(object): 
    def __enter__(self):
        self.start = time.time()
        return self
 
    def __exit__(self, *args):
        self.end = time.time()
        self.secs = self.end - self.start
        self.msecs = self.secs * 1000  # millisecs

dic = {
    "load": "HTTP/1.1 200 OK\r\nServer: WS\r\nContent-Type: application/javascript\r\nContent-Length: 6\r\nCache-Control: public, max-age=86400\r\nDate: Sun, 01 Feb 2015 03:23:11 GMT\r\n\r\nhello!",
    "src": "1.2.3.4",
    "seq": 1329437296,
    "ack": 30287505,
    "dst": "5.6.7.8",
    "dport": 57276,
    "sport": 80	
}


with Timer() as t:
	for i in xrange(1, 10000):
		json.dumps(dic)
print "json.dumps: %s ms" % t.msecs

with Timer() as t:
	for i in xrange(1, 10000):
		cPickle.dumps(dic)
print "cPickle.dumps: %s ms" % t.msecs

with Timer() as t:
	for i in xrange(1, 10000):
		pickle.dumps(dic)
print "pickle.dumps: %s ms" % t.msecs

with Timer() as t:
	for i in xrange(1, 10000):
		ujson.dumps(dic)
print "ujson.dumps: %s ms" % t.msecs

o = zlib.compressobj(1)
with Timer() as t:
	for i in xrange(1, 10000):
		o.compress(ujson.dumps(dic))
print "zlib.compressObj + ujson.dumps: %s ms" % t.msecs

with Timer() as t:
	for i in xrange(1, 10000):
		zlib.compress(ujson.dumps(dic), 1)
print "zlib.compress + ujson.dumps: %s ms" % t.msecs

