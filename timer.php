<?php

use Workerman\Worker;

require_once __DIR__ . '/Workerman/Autoloader.php';

$worker = new Worker('tcp://0.0.0.0:8080');
$worker->count = 4;
$worker->name = 'MyWorkermanPro';

//是否以daemon(守护进程)方式运行
//Worker::$daemonize = true;

//worker实例1有4个进程，进程id编号将分别为0/1/2/3
$worker->onWorkerStart = function($worker)
{
    echo "worker->id={$worker->id}\n";
    //只在id编号为0的进程上设置定时器，其他1、2、3号进程不设置定时器
    if($worker->id === 0) {
        \Workerman\Lib\Timer::add(1, function(){
           echo "4个worker进程，只在0号进程设置定时器\n";
        });
    }
};

//设置worker收到reload信号后执行的回调
$worker->onWorkerReload = function ($worker) {
    foreach ($worker->connections as $connection) {
        $connection->send("worker reloading");
    }
};

$worker->onConnect = function ($connection) {
    echo "new connection from ip:" . $connection->getRemoteIp() . "\n";
};

//connection 连接对象，用于操作客户端连接，如发送数据/关闭连接等
//data 客户端上发来的数据，如果worker指定了协议，则$data是对应协议decode了的数据
$worker->onMessage = function ($connection, $data) {
    var_dump($data);
    $connection->send("receive success");
};


$worker->onClose = function ($connection) {
    echo "connection closed\n";
};

// worker实例2有两个进程，进程id编号将分别为0、1
$worker2 = new Worker('tcp://0.0.0.0:8081');
// 设置启动2个进程
$worker2->count = 2;
// 每个进程启动后打印当前进程id编号即 $worker2->id
$worker2->onWorkerStart = function($worker2)
{
    echo "worker2->id={$worker2->id}\n";
};

// 运行worker
Worker::runAll();
