<?php

use Workerman\Worker;

require_once __DIR__ . '/Workerman/Autoloader.php';

$worker1 = new Worker('tcp://0.0.0.0:8080');
$worker1->count = 4;
//workerʵ��1��4�����̣�����id��Ž��ֱ�Ϊ0/1/2/3
$worker1->onWorkerStart = function($worker1)
{
    echo "worker1->id={$worker1->id}\n";
};

// workerʵ��2���������̣�����id��Ž��ֱ�Ϊ0��1
$worker2 = new Worker('tcp://0.0.0.0:8081');
// ��������2������
$worker2->count = 2;
// ÿ�������������ӡ��ǰ����id��ż� $worker2->id
$worker2->onWorkerStart = function($worker2)
{
    echo "worker2->id={$worker2->id}\n";
};

// ����worker
Worker::runAll();
