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

        $this->ws->set([
            'task_worker_num' => 2,
            'task_enable_coroutine' => true
        ]);

        $this->ws->on('open', array($this, 'onOpen'));
        $this->ws->on('message', array($this, 'onMessage'));
        $this->ws->on('task', array($this, 'onTask'));
        $this->ws->on('close', array($this, 'onClose'));

        $this->ws->start();
    }

    /**
     * 当 WebSocket 客户端与服务器建立连接并完成握手后会回调此函数。
     * @param $server
     * @param $request
     */
    public  function onOpen($server, $request)
    {
        $data = 'a';

        $server->task($data);
    }

    public  function onTask($server, $frame)
    {
        // 定义记录文件行文件
        $line_file_name = __DIR__ . '/num.txt';
        // 写入记录文件行文件，用户读取文件第几行的数据，因为 swoole 里的定时器只执行一遍代码，无法改变变量的值，所以只能通过文件写入的方式
        file_put_contents($line_file_name, 1);

        /**
         * 通过 php 本地读取文件的方式，一行一行的读取文件的内容
         */
        /*
        $file           = __DIR__ . '/a.txt';

        Swoole\Timer::tick(1000, function ($timer_id, $file, $line_file_name, $server) {

            $num = file_get_contents($line_file_name);

            $file_content = '';
            go(function () use (&$file_content, $file, $num)
            {
                $file_content = $this->getLine($file, $num, filesize($file));
            });

            var_dump($file_content);

            if (strpos($file_content, 'end') == 'end') {
                Swoole\Timer::clear($timer_id);
            } elseif (!empty($file_content)) {
                file_put_contents($line_file_name, $num + 1);
                foreach ($server->connections as $fd){
                    $server->push($fd, $file_content);
                }
            }

        }, $file, $line_file_name, $server);*/

        Swoole\Timer::tick(1000, function ($timer_id, $line_file_name, $server) {

            $num = file_get_contents($line_file_name);

            $file_content = '';
            go(function () use (&$file_content, $num)
            {
                $file_content = shell_exec('sshpass -p\'8(GuangNai)6*6(WangLuo)8*v*!@#123\' ssh -p 9669 root@122.114.222.172 sed -n \'' . $num . 'p\' /home/getData/log/logs.txt');
            });

            var_dump($file_content);

            if (strpos($file_content, 'end') == 'end') {
                Swoole\Timer::clear($timer_id);
            } elseif (!empty($file_content)) {
                file_put_contents($line_file_name, $num + 1);
                foreach ($server->connections as $fd){
                    $server->push($fd, $file_content);
                }
            }

        }, $line_file_name, $server);

    }

    /**
     * 当服务器收到来自客户端的数据帧时会回调此函数。
     * @param $server
     * @param $frame
     */
    public  function onMessage($server, $frame)
    {
        print_r($frame);
    }

    public  function onClose($server, $fd, $reactorId)
    {
        print_r($fd. '-----' . $reactorId);
    }

    /**
     * 获取指定行内容
     * @param string $file 文件路径
     * @param int $line 行数
     * @param int $length 指定行返回内容长度
     * @return bool|string|null
     */
    public function getLine($file, $line, $length = 40960)
    {
        // 初始化返回
        $returnTxt = null;
        // 行数
        $i = 1;

        $handle = @fopen($file, "r");
        if ($handle) {
            while (!feof($handle)) {
                $buffer = fgets($handle, $length);
                if($line == $i) $returnTxt = $buffer;
                $i++;
            }
            fclose($handle);
        }

        return $returnTxt;
    }
}

new WebSocket();