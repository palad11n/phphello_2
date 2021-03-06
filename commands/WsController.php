<?php


namespace app\commands;

use Amp\Loop;
use Amp\Promise;
use Amp\Socket\Server as SocketServer;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\Router;
use Amp\Http\Server\StaticContent\DocumentRoot;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Websocket\Server\Websocket;
use app\commands\ws\wsHandler;
use Monolog\Logger;
use yii\console\Controller;
use yii\console\ExitCode;
use function Amp\ByteStream\getStdout;

/**
 * Обработка веб-сокета
 * @package app\controllers
 */
class WsController extends Controller
{
    /*
     * @var websocket
     */
    public $ws;

    public function init()
    {
        parent::init();
        $this->ws = new Websocket(new wsHandler());
    }

    public function actionRun()
    {
        Loop::run(function (): Promise {
            $sockets = [
                SocketServer::listen('0.0.0.0:1337')
            ];

            $router = new Router;
            $router->addRoute('GET', '/websocket/list/index/broadcast', $this->ws);
            $logHandler = new StreamHandler(getStdout());
            $logHandler->setFormatter(new ConsoleFormatter);
            $logger = new Logger('server');
            $logger->pushHandler($logHandler);
            $server = new HttpServer($sockets, $router, $logger);

            return $server->start();
        });

        return ExitCode::OK;
    }
}