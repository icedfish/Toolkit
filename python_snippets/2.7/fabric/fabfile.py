# -*- coding: utf-8 -*-

__author__ = 'yubing'

# http://docs.fabfile.org/en/1.10/tutorial.html


# fab hello
def hello():
    print("Hello world!")
    pass


# fab hello:name=Bing
# fab hello:Bing
def hello(name="world"):
    print("Hello %s!" % name)
    pass


# http://docs.fabfile.org/en/1.10/api/core/context_managers.html#fabric.context_managers.settings
from fabric.api import *
def fail():
    with settings(
        hide('warnings', 'running', 'stdout', 'stderr'),
        warn_only=True
    ):
        local("exit(1)")
        local("echo 'haha'") #hidden by setting()

        if local('ls /etc/issue'):
            print('Linux')
        else:
            print('Mac')
    pass


# promote for people input
from var_dump import var_dump
from fabric.contrib.console import confirm
def promote():
    with settings(warn_only=True):
        result = local('python xx.py', capture=True)
    var_dump(result)
    if result.failed and not confirm("Tests failed. Continue anyway?"):
        abort("Aborting at user request.")


def test():
    with lcd('/tmp'):
        local()

