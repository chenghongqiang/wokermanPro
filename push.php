<?php
/**
 * Created by PhpStorm.
 * User: kewin.cheng
 * Date: 2018/7/2
 * Time: 16:39
 */

use Workerman\Worker;
require_once './Workerman/Autoloader.php';

$worker = new Worker('websocket://0.0.0.0:8080');

$worker->count = 1;

$worker->onWorkerStart = function($worker) {

    $inner_text_worker = new Worker('text://0.0.0.0:8081');
    $inner_text_worker->onMessage = function ($connection, $buffer) {
        $data = json_decode($buffer, true);
        $uid = $data['uid'];

        //ͨ��workerman ��uid��ҳ����������
        $ret = sendMessageByUid($uid, $buffer);
        //������������
        $connection->send($ret ? 'ok' : 'fail');

    };

    $inner_text_worker->listen();

};

$worker->uidConnections = array();
$worker->onMessage = function ($connection, $data) {
    global $worker;

    if(!isset($connection->uid)) {
        $connection->uid = $data;
        $worker->uidConnections[$connection->uid] = $connection;

        return;
    }

};

$worker->onClose = function ($connection){
    global $worker;

    if(isset($connection->uid)) {
        unset($worker->uidConnections[$connection->uid]);
    }
};

// ��������֤���û���������
function broadcast($message)
{
    global $worker;
    foreach($worker->uidConnections as $connection)
    {
        $connection->send($message);
    }
}


function sendMessageByUid($uid, $message) {
    global $worker;

    if(isset($worker->uidConnections[$uid])) {
        $connection = $worker->uidConnections[$uid];
        $connection->send($message);
        return true;
    }

    return false;

}

//�������е�worker
Worker::runAll();




