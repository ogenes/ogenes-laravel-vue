<div>
<pre>
你好，
感谢你注册 {{env('APP_URL', 'http://img.dev.com')}}。
你的登录邮箱为：{{$email}} 。请点击以下链接激活账号，

<a href="{{$url}}!" target="_blank">{{$url}}!</a>

如果以上链接无法点击，请将上面的地址复制到你的浏览器访问。
</pre>
</div>
