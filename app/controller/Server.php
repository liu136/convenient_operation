<?php
namespace app\controller;

use app\BaseController;

class Server extends BaseController
{
    public function pullData()
    {
        echo 1; exit;
        $cmd = 'type a.txt';
        $res = shell_exec($cmd);
        print_r($res);
        return 1;
    }
}
