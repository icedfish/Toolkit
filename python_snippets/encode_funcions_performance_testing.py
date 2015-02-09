# -*- coding: UTF-8 -*-
import json,ujson
import zlib
import base64
import msgpack

#对比常见的Python Encoding方式效率差别

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

dic_str = ujson.dumps(dic)

print '## base64 is faster!'
with Timer() as t:
	for i in xrange(1, 10000):
		base64.b64encode(dic_str)
print "base64: %s ms" % t.msecs

with Timer() as t:
	for i in xrange(1, 10000):
		s = zlib.compress(dic_str, 1)
print "zlib: %s ms" % t.msecs

print '## base64 大了 33%'
print 'source:', len(dic_str)
print 'base64:', len(base64.b64encode(dic_str))
print 'zlib:', len(zlib.compress(dic_str, 1))

print '## 综合考虑性能'
with Timer() as t:
	for i in xrange(1, 10000):
		base64.b64encode(msgpack.packb(dic))
print "base64 + msgpack: %s ms" % t.msecs

with Timer() as t:
	for i in xrange(1, 10000):
		zlib.compress(ujson.dumps(dic), 1)
print "zlib + ujson: %s ms" % t.msecs

print '## 综合考虑大小， base64+msgpack大了10%'
print 'source:', len(dic_str)
print 'base64:', len(base64.b64encode(msgpack.packb(dic)))
print 'zlib:', len(zlib.compress(ujson.dumps(dic), 1))

