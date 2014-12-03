# -*- coding: UTF-8 -*-

import tornado.ioloop
import tornado.web

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
	application.listen(8888)
	tornado.ioloop.IOLoop.instance().start()
