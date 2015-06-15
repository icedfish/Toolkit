用了半年的Laravel，越来越喜欢他了，他大量的设计都和我的观念相互印证，而且往往更加精妙和通用。今天就提笔记一个关于CSRF防护的问题。

### 我以前的方案
在我之前设计和使用的系统中，为了避免有人不小心写出CSRF漏洞，我会全局禁止$_REQUEST的使用以及混用POST和GET的行为，要求开发人员分清每个传入数据是POST还是GET。

于是我会在Reqeust对象上增加get($field_name, $default), post($field_name, $default) 的方法。并且直接在框架代码启动的时候将全局变量$_REQUEST置为空数组。

### Laravel的方案
#### Part 1 [Global CSRF Protection](http://laravel.com/docs/master/routing#csrf-protection)
Laravel推荐在全局注册VerifyCsrfToken的Middleware，对所有Post,Put,Delete请求自动校验是否带合法的_csrf token。而要在表单中添加这个Token，只需要在form中加一行：

```php
 <?php echo csrf_field(); ?> 
 
 // blade:
 
 {!! csrf_field() !!}
```
获取表单值的方法：

```php
$request->input('name', 'default name');
```

#### Part 2 路由层面区分Post/Get
```php
// 同个Path下的Get，Post请求会根据配置不同进入不同的处理逻辑
Route::get('/path', 'XXXController@func_get');
Route::post('/path', 'XXXController@func_post');
```

我觉得Laravel这么设计比我高明在两个地方：

1. 对于新人，如果在Laravel中新增了一个表单，Post发现提交的时候提示CSRF校验失败，他很容易知道有这么个校验，且开发环境下可以友好的提示他如何搞定这个问题。  
    而我的做法由于和PHP原生行为不一致，第一次碰到的人会很奇怪，就算花时间最终搞明白了，他也会觉得自己掉了个坑，因为我没有任何提示。（其实现在想来是可以有提示的，应该将$_REQUEST改成一个每次调用都抛Exception给予提示的闭包）。  

2. Laravel可以方便的配置例外情况。本周即将发布的5.1-LTS版包涵一个我很期待的改进就是可以通过配置，忽略特定路径下的CSRF校验，以便兼容第三方代码组件和第三方的Post数据回传。[详见](http://laravel.com/docs/master/routing#csrf-excluding-uris)


### 依然可能有的问题
我之前的项目中，到后来，会发现有人写这样的代码： 

```php
$val = $request->get('name', $request->post('name', 'defailt name'));
```
这么写有时候是真的需要；但大多数时候只是因为懒/或者copy来的代码自己也没搞明白。

在Laravel中也无法完全避免这类问题，因为路由配置的时候除了Rouet::get() Route::post() 还有个大杀器 Route::any()，它同时兼容get和post请求，间接引入了同样的问题。

### ~遗憾~
虽然在Laravel中，上述问题我可以增加UnitTest，检查大家对any()的使用，但实在不够优雅，谁有更好的思路，非常希望你能来信告诉我 icedfish@gmail.com
