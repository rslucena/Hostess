<?php

declare(strict_types=1);

require_once 'Environments.php';

use app\Bootstrap\Builder;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
use Workerman\Protocols\Http\Response;
use Workerman\Protocols\Http\Session\FileSessionHandler;
use Workerman\Worker;

require_once sprintf("%s/autoload.php", DIR_VENDOR);

require_once sprintf("%s/Settings.php", DIR_CONFIG);

//Create:Routes
Builder::Routes();

//Create:DataBaseConnect
global $DB;
$DB = new Workerman\MySQL\Connection(DB_SERVE, DB_PORT, DB_USER, DB_PASS, DB_NAME);

//Set:SessionPath
FileSessionHandler::sessionSavePath(DIR_SESSIONS);


//Create:Server
$HTTPServer = new Worker(APP_PROTOCOL . "://" . SERVER_IP . ":" . SERVER_PORT);

$HTTPServer->name = SERVER_NAME;
$HTTPServer->count = SERVER_WORKER;
$HTTPServer::$stdoutFile = DIR_LOGS . '/HTTP/Runtime.log';

/**
 * Start automatic system tasks.
 * Controlled by worker timer.
 * @param Worker $CronJobs
 */
$HTTPServer->onWorkerStart = function (Worker $CronJobs) use ($DB) {
    //Timer::add(10, 'send_mail', array(), false);
};

/**
 * Event emitted upon receiving a new connection
 * @param TcpConnection $Connection
 * @param Request $Request
 */
$HTTPServer->onMessage = function (TcpConnection $Connection, Request $Request) {

    if ($Request->method() === 'OPTIONS') {

        $Connection->send(new Response(200, array(
            'Content-Type' => 'application/json',
            'Access-Control-Allow-Credentials' => true,
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS'
        )));

        return;
    }

    //Response:Controller
    $ResponseAction = Builder::execute($Request);

    //CreateType:Response
    $Return = new Response();
    $Return->withStatus($ResponseAction['Function']['CodeResponse']);
    $Return->withBody($ResponseAction['Function']['Result']);

    //Set:Header
    $Header['x-authorization-token'] = $ResponseAction['User']['Token'];
    $Header['Content-Type'] = $ResponseAction['Platform']['Content-Type'];

    //Set:Format File
    if (!empty($ResponseAction['Platform']['IsFile'])) {

        $Return->withStatus(200);

        $IsModified = date('D, d M Y H:i:s', filemtime(DIR_PUBLIC . $Request->path() ) )  . ' ' . date_default_timezone_get();

        if ($IsModified === $Request->header('if-modified-since')) {
            $Connection->send(new Response(304));
            return;
        }

        $Return->withFile(DIR_PUBLIC . $Request->path());

        $Connection->send($Return);
    }

    //Set:Header
    $Return->withHeaders($Header);

    if ($ResponseAction['Redirect']['force']) {
        $Return = new Response(302, ['Location' => $ResponseAction['Redirect']['location']]);
    }

    //Send:Response
    $Connection->send($Return);

};

/**
 * Run all workers
 */
Worker::runAll();
