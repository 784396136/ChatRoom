<?php
// WebSocket 服务器

// 引入composer自动加载类
require('../vendor/autoload.php');
use Firebase\JWT\JWT;

// 引入websocket自动加载类
require_once "../Workerman-master/Autoloader.php";
use Workerman\Worker;

// 实例化 Worker 类对象
$worker = new Worker('websocket://0.0.0.0:9999');
// 设置进程数
$worker->count = 1;

// 保存所有的用户
$users = [];
// 保存所有客户端
$usersConn = [];

// 绑定连接的回调函数，这个函数会在有客户端连接时调用
// 参数：TcpConnection 类的对象，代表每个客户端
$worker->onConnect = function( $connection ) {
    
    $connection->onWebSocketConnect = function ($connection, $http_header) {
        global $users, $worker , $usersConn;
        // 解析令牌
        try
        {
            // 获取并解析令牌
            $key = 'jwt_code';
            $data = JWT::decode($_GET['token'],$key,array('HS256'));
            // 保存到$users中
            $users[$data->id] = $data->name;
            $usersConn[$data->id] = $connection;
            // 将用户ID保存到当前客户端中
            $connection->uid = $data->id;
            // 返回用户名
            $connection->send(json_encode([
                'type'=>'user',
                'username'=>$data->name,
            ]));
            // 连接成功发送消息通知所有用户
            foreach($worker->connections as $c)
            {
                $c->send(json_encode([
                    'type'=>'users',
                    'users'=>$users,
                ]));
                $c->send(json_encode([
                    'type'=>'system',
                    'message'=>"系统消息: 用户{$data->name}上线了"
                ]));
            }
        }
        catch(\Firebase\JWT\ExpiredException $e)
        {
            $connection->close();
        }
        catch(\Exception $e)
        {
            $connection->close();
        }
    };
};
// 接收消息
$worker->onMessage = function($connection, $data) {
    global $worker,$users;
    // 判断是群发 还是私聊
    $data = explode(':',$data);
    if($data[0]=='all')
    {
        // 删除标识
        unset($data[0]);
        // 转回字符串
        $data = implode(':',$data);
        // 时区相差8小时
        $now = date('H:i',strtotime ("+8 hours"));
        foreach($worker->connections as $c)
        {
            $c->send(json_encode([
                'type'=>'message',
                'message'=>$data,
                'username'=>$users[$connection->uid],
                'time'=>$now,
            ]));
        }
    }
    else
    {
        global $usersConn;
        $id = $data[0];
        // 删除标识
        unset($data[0]);
        $now = date('H:i',strtotime ("+8 hours"));
        // 发送给私聊对象
        $usersConn[$id]->send(json_encode([
            'type'=>'to',
            'message'=>implode(':',$data),
            'username'=>$users[$connection->uid],
            'time'=>$now,
        ]));
        // 发送给自己
        $usersConn[$connection->uid]->send(json_encode([
            'type'=>'message',
            'message'=>implode(':',$data),
            'username'=>$users[$connection->uid],
            'time'=>$now,
        ]));
    }
    
};

// 当有客户端断开时调用
$worker->onClose = function($connection)
{
    global $worker,$users;
    // 当用户断开时从列表中删除
    $username = $users[$connection->uid];
    unset($users[$connection->uid]);
    
    foreach($worker->connections as $c)
    {
        $c->send(json_encode([
            'type'=>'users',
            'users'=>$users,
        ]));
        $c->send(json_encode([
            'type'=>'system',
            'message'=>"系统消息: 用户{$username}下线了",
        ]));
    }
};

// 运行
Worker::runAll();