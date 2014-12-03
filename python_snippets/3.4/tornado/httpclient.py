# -*- coding: UTF-8 -*-

import tornado.httpclient as th

client = th.HTTPClient()

for i in xrange(4000, 10000) :
    print "%d" % i ,
    try:
        rs = client.fetch("http://bdwm.hsmkj.net/js/%d.js" % i)
        print rs.headers
    except th.HTTPError :
        print " not found"

