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

        // 生成的拉取数据文件
        file_put_contents('/home/www/dataSh/'. $pull_data_file_name .'.sh', $pull_data_file);

        shell_exec('scp -p 9669 /home/www/dataSh.' . $pull_data_file_name .'.sh' . ' root@122.114.222.172 /home/getData/dataSh');
        $res = shell_exec('ssh -p 9669 root@122.114.222.172 bash /home/getData/main.sh ' . $pull_data_file_name . '.sh');

        // 删除生成的拉取数据文件
        shell_exec('rm -rf /home/www/dataSh/' . $pull_data_file_name .'.sh');

        return $res;
    }
}
