
### 运行
```shell
#clone项目
[root@ALiYunOgenes www]# git clone git@github.com:ogenes/cynic-img.git

[root@ALiYunOgenes www]# cd cynic-img/

### 复制配置文件， 并修改成自己的配置
[root@ALiYunOgenes cynic-img]# cp .env.example .env
[root@ALiYunOgenes cynic-img]# vim .env

### composer
[root@ALiYunOgenes cynic-img]# composer install -vvv

### key:generate
[root@ALiYunOgenes cynic-img]# php artisan key:generate

[root@ALiYunOgenes cynic-img]# npm install && npm run prod

```


### 开发
```shell
[root@ALiYunOgenes cynic-img]# php artisan ide-helper:generate
[root@ALiYunOgenes cynic-img]# npm run watch
```

## nginx 配置示例
```shell
server {
    listen  80;

    server_name  img.dev.com;
    root   /var/www/cynic-img/current/public;
    index  index.php index.html index.htm;

    error_log /home/logs/nginx/cynic_error.log;
    access_log /home/logs/nginx/cynic_access.log;

    location / {
        try_files $uri $uri/ /index.php$is_args$query_string;
    }

    # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
    location ~ \.php$ {
        fastcgi_pass unix:/home/run/php/php74-fpm.sock;
        fastcgi_index  index.php;
        include        fastcgi_params;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param SCRIPT_NAME $fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }
}
```
