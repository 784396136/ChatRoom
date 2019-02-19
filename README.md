# 基于WebSocket聊天室

### 启动时使用启动PHP内置服务器

~~~	
php -S localhost:9999 
~~~



### 启动后访问

 http://localhost:9999/ChatRoom/login.html



### 启动WebSocket 服务器 

~~~
php server.php start
~~~

### 使用MySQL数据库

~~~
需要导入user.sql文件

用户名 ： cunye  yinrui  admin

登录密码为 123123

~~~



在ChatRoom/login.php 中修改数据库的名称、用户名和密码

需要在不同浏览器中登录 