<?php

use Workerman\Worker;

require_once __DIR__ . '/Workerman/Autoloader.php';

$worker1 = new Worker('tcp://0.0.0.0:8080');
$worker1->count = 4;
//worker实例1有4个进程，进程id编号将分别为0/1/2/3
$worker1->onWorkerStart = function($worker1)
{
    echo "worker1->id={$worker1->id}\n";
    //只在id编号为0的进程上设置定时器，其他1、2、3号进程不设置定时器
    if($worker1->id === 0) {
        \Workerman\Lib\Timer::add(1, function(){
           echo "4个worker进程，只在0号进程设置定时器\n";
        });
    }
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
