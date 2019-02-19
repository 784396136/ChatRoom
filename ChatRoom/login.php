<?php
// 引入composer的自动加载类
require('../vendor/autoload.php');

// 引入JWT
use Firebase\JWT\JWT;

// 连接数据库
$pdo = new \PDO('mysql:host=127.0.0.1;dbname=workerman','root','');
$pdo->exec('set names utf8');

// 获取原始数据
$post = file_get_contents("php://input");
$_POST = json_decode($post,true);

// 预处理   判断用户名和密码是否正确
$stmt = $pdo->prepare("SELECT * from user WHERE username = ? AND password = ?");
$stmt->execute([
    $_POST['username'],
    md5($_POST['password']),
]);

// 从数据库中获取数据
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 返回结果
if($user)
{
    // jwt 加密密码
    $key = 'jwt_code';
    // 要加密的数据
    $data = [
        'id' => $user['id'],
        'name' => $user['username']
    ];

    // 生成令牌返回给前端
    $jwt = JWT::encode($data,$key);
    echo json_encode([
        'code' => '200',
        'jwt' => $jwt
    ]);
}
else
{
    echo json_encode([
        'code' => '404',
        'error' => '用户名或密码错误',
    ]);
}