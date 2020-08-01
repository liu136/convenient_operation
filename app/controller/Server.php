<?php
namespace app\controller;

use app\BaseController;

class Server extends BaseController
{
    public function pullData()
    {
        // 获取 json 格式传递的数据
        $all = file_get_contents('php://input');
        // 解析 json 格式数据
        $all = json_decode($all);

        if (empty($all)) {
            return $this->errorJson('data is null');
        }

        // 生成 拉取 数据
        $pull_data_file = 'Destination_IP=' . $all->ip . "\nTarget_database_name=" . $all->database. "\nlocal_Database=" . $all->local_database . "\nactorid=" . $all->actor_id . "\nvirtual=" . $all->virtual;
        // 生成拉取数据文件名
        $pull_data_file_name = time();

        // 生成的拉取数据文件
        file_put_contents('/home/www/dataSh/'. $pull_data_file_name .'.sh', $pull_data_file);

        // 移动拉取数据文件到指定服务器
        $r = shell_exec('sshpass -p\'8(GuangNai)6*6(WangLuo)8*v*!@#123\' scp -P 9669 /home/www/dataSh/' . $pull_data_file_name .'.sh' . ' root@122.114.222.172:/home/getData/dataSh 2>&1 &');
//        shell_exec('scp -P 9669 /home/www/dataSh/' . $pull_data_file_name .'.sh' . ' root@122.114.222.172:/home/getData/dataSh 2>&1 &');
//        exec("rsync -avz --progress -e 'ssh -p 9669' " . "/home/www/dataSh/" . $pull_data_file_name .".sh" ." 122.114.222.172:/home/getData/dataSh", $res, $status);
        // 执行拉取数据 shell 脚本
        $res = shell_exec('sshpass -p\'8(GuangNai)6*6(WangLuo)8*v*!@#123\' ssh -p 9669 root@122.114.222.172 bash /home/getData/main.sh ' . $pull_data_file_name . ' 2>&1 &');

        // 删除生成的拉取数据文件
        shell_exec('rm -rf /home/www/dataSh/' . $pull_data_file_name .'.sh 2>&1 &');

        // 返回执行结果
        return $this->successJson('pull data success', $res);
    }

    /**
     * 接口返回 JSON格式
     *
     * @param string $status  状态
     * @param string $message 提示信息
     * @param array $data     返回数据
     * @return \think\response\Json
     */
    public function returnJson($status = '', $message = '', $data = [])
    {
        return json([
            'status' => $status,
            'msg'    => $message,
            'data'   => $data
        ], 200, ['charset' => 'utf-8']);
    }

    /**
     * 接口返回成功 JSON格式
     *
     * @param string $message 提示信息
     * @param array $data 返回数据
     * @return \think\response\Json
     */
    public function successJson($message = 'success', $data = [])
    {
        return json([
            'status' => config('json.success'),
            'msg'    => $message,
            'data'   => $data
        ], 200, ['charset' => 'utf-8']);
    }

    /**
     * 接口返回错误JSON 格式
     *
     * @param string $message 提示信息
     * @param array $data 返回数据
     * @return \think\response\Json
     */
    public function errorJson($message = 'error', $data = [])
    {
        return json([
            'status' => config('json.error'),
            'msg'    => $message,
            'data'   => $data
        ], 200, ['charset' => 'utf-8']);
    }
}
