from bottle import *

@post('/<name>')
def data(name):
	return template('<b>Hello {{name}}</b>!', name=name)

@get('/<name>')
def index(name):
	form = '<form method="post" action="/{{name}}"> <input name="data"><input type="submit" value="submit"> </form>'
	return template(form, name=name)

run(host='localhost', port=9999)