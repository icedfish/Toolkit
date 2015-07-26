平时开发过程中经常碰到写个数据处理脚本，统计脚本的情况，都需要在Laravel项目的环境下执行，而官方提供的artisan命令却不支持直接执行php脚本文件，这里记录下自己做的一个Command，用来解决这类问题。


1. 在Console/Commands下面添加RunFile.php [source code](https://gist.github.com/icedfish/d337740fbf728ed6dd6a)
2. php artisan run /path/xxx.php 即可

Simple but works : )
