# -*- coding: UTF-8 -*-

import tornado.ioloop
import tornado.web
import tornado.httpserver

class MainHandler(tornado.web.RequestHandler):
    # stop etag from https://gist.github.com/andreadipersio/7526464
    def compute_etag(self):
        self.set_header('Server', 'XooX')
        return None

    def get(self):
        self.write("hello")

class ErrorHandler(MainHandler):
    def get(self):
        self.write("恭喜您，您穿越了！")

application = tornado.web.Application([
    (r"/", MainHandler),
    (r".*", ErrorHandler),
])

if __name__ == "__main__":
	# 单进程
	application.listen(8888)
	tornado.ioloop.IOLoop.instance().start()
	
	#多进程
	# http_server = tornado.httpserver.HTTPServer(application)
	# http_server.bind(8888, '127.0.0.1')
	# http_server.start(num_processes=2) # tornado将按照cpu核数来fork进程
	# tornado.ioloop.IOLoop.instance().start()
