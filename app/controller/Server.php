<?php
namespace app\controller;

use app\BaseController;

class Server extends BaseController
{
    public function pullData()
    {
        $all = file_get_contents('php://input');
        $all = json_decode($all);

        $pull_data_file = 'Destination_IP=' . $all->ip . "\r\nTarget_database_name=" . $all->database. "\r\nlocal_Database=" . $all->local_database . "\r\nactorid=" . $all->actor_id . "\r\nvirtual=" . $all->virtual;
        $pull_data_file_name = time();

        file_put_contents('./home/getData/dataSh/'. $pull_data_file_name .'.sh', $pull_data_file);

        $res = shell_exec('ssh -p 9669 root@122.114.222.172 bash /home/getData/main.sh ' . $pull_data_file_name . '.sh');

        return $res;
    }
}
