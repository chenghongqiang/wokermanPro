<?php
/**
 * Created by PhpStorm.
 * User: kewin.cheng
 * Date: 2018/6/29
 * Time: 16:41
 */

require_once __DIR__ . '/Workerman/Autoloader.php';

use Workerman\Worker;
use Workerman\Lib\Timer;

define("HEATBEAT_TIME", 30);

$worker = new Worker("tcp://0.0.0.0:8080");

$worker->onWorkerStart = function ($worker) {
    Timer::add(1, function() use($worker){

        $now = time();
        foreach($worker->connections as $connection) {

            if(empty($connection->lastMessageTime)) {
                $connection->lastMessage = $now;
                continue;
            }

            if( $now - $connection->lastMessage > HEATBEAT_TIME) {
                $connection->close();
            }
        }

    });
};

$worker->onMessage = function ($connection, $msg) {
    $connection->lastMessageTime = time();
};

$worker->onError = function($connection, $code, $msg)
{
    echo "error $code $msg\n";
};


Worker::runAll();

