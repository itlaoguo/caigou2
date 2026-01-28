## 采购管理系统
## 对接阿里云linkedmall的采购管理系统

## 前端项目

居于腾讯Tdesign UI框架

## 后台

居于catchadmin开源项目，使用laravel开发

## 部署

node 版本 ===》v22.12.0
php 版本 ====》php8.2

先部署后台，虚拟主机目录指到项目public文件夹下，配置nginx

```
location / {
    index index.php;
    try_files $uri $uri/ /index.php?$query_string;
}
```

- 部署前端；主要是前端项目部署到nginx后，统一入口为index.html，并且将访问 https://www.domain.com/api/ 前缀的url反向代理到后台
 
```
  location / {
  index index.html;
    try_files $uri $uri/ /index.html;
  }

  location /api/ {
    proxy_pass http://192.168.15.173:10020/api/;
  }
```







