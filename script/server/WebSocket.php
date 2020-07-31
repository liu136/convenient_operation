<?php
/**
 * Created by PhpStorm.
 * User: Kris Liu
 * Date: 2020/7/31
 * Time: 20:24
 */

class WebSocket
{
    const HOST = '0.0.0.0';
    const POST = '9501';

    public $ws = null;

    public function __construct()
    {
        $this->ws = new Swoole\WebSocket\Server(self::HOST, self::POST);

        $this->ws->on('open', array($this, 'onOpen'));
        $this->ws->on('message', array($this, 'onMessage'));
        $this->ws->on('close', array($this, 'onClose'));

        $this->ws->start();
    }

    public  function onOpen($server, $request)
    {
        print_r($server);
        print_r($request);
    }

    public  function onMessage($server, $frame)
    {
        print_r($frame);
    }

    public  function onClose($server, $fd, $reactorId)
    {
        print_r($fd. '-----' . $reactorId);
    }
}

new WebSocket();