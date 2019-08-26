#!/usr/local/bin/python3
# coding=UTF-8
# 线上sql在统计的时候需要去参数化，才能比较好的分辨到底有多少个不同的查询， 这里实现了一个将复杂sql转为 md5 hash string的逻辑。
# 代码改写自 https://github.com/xuclachina/dbatools/blob/b20c151d981edc73738506ee4ee97a8460bc195c/slow_log_parser/slow_log_parser.py

import hashlib
import re
import urllib.parse


#SELECT 'a', "b" from table where a = '这是"一句\"断\'了的话' And b <> '特别""的"""结尾\\\'\\' And c in ('单引号', "双引号") and d in ( select 'vv' from table_b ) ORDER BY table.id DESC limit 100,10
#python 里面表达复杂引号关系比较麻烦，所以为了测试方便，代码里面将上述sql encode了下：
string='SELECT+%27a%27%2c+%22b%22+from+table+where+a+%3d+%27%e8%bf%99%e6%98%af%22%e4%b8%80%e5%8f%a5%5c%22%e6%96%ad%5c%27%e4%ba%86%e7%9a%84%e8%af%9d%27+And+b+%3c%3e+%27%e7%89%b9%e5%88%ab%22%22%e7%9a%84%22%22%22%e7%bb%93%e5%b0%be%5c%5c%5c%27%5c%5c%27+And+c+in+(%27%e5%8d%95%e5%bc%95%e5%8f%b7%27%2c+%22%e5%8f%8c%e5%bc%95%e5%8f%b7%22)+and+d+in+(+select+%27vv%27+from+table_b+)+ORDER+BY+table.id+DESC+limit+100%2c10'
sql = urllib.parse.unquote_plus(string)

line_no_number = re.sub(r'\d+', "?", sql)
line_no_param = re.sub(r'([\'\"])([^\\]|\\.(?#如果有转义字符，必然是成对出现))*?\1(?#匹配和前面相同的引号)', "?", line_no_number)    # debug see https://regex101.com/r/edor2M/6
sql_pattern = re.sub(r'\(\?.+?\)', "(?)", line_no_param)

m1 = hashlib.md5()
m1.update(str(sql_pattern).encode('utf-8'))
sql_hash = m1.hexdigest()

print("\nsql: \n" + sql + "\nsql_pattern: \n" + sql_pattern + "\nsql_hash: \n" + sql_hash)