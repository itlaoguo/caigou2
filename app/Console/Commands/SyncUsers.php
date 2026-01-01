<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->getUsers();
    }

    private function getUsers(){
        try {

            $this->info('发起erp接口请求...');

            $client = new Client([
                'base_uri' => 'https://www.iferp.local/',    // 基础 URI
                'verify' => false,
            ]);

            $page = 1;
            $page_size = 100;

            while (true){
                $parameters = array(
                    'page' => $page,
                    'page_size' => $page_size
                );

                $response = $client->get('synch/User.php',array('query'=>$parameters));
                $result = json_decode($response->getBody(), true);

                if($result['status'] === 0 || empty($result['data'])){
                    break;
                }

                // 清洗数据
                $this->info("处理第 {$page} 页数据");
//                echo '<pre>';print_r($result);exit();
                foreach ($result['data'] as $employee){

                    $employee_data = array(
                        'id' => $employee['user_id'],
                        'username' => $employee['cn_name'],
                        'password' => $employee['password'],
                        'email' => $employee['email'],
                        'avatar' => $employee['img'],
                        'remember_token' => '',
                        'department_id' => 0,
                        'creator_id' => 0,
                        'status' => $employee['status'],
                        'login_ip' => $employee['reg_ip'],
                        'login_at' => $employee['last_login_time'],
                        'created_at' => $employee['reg_time'],
                        'updated_at' => $employee['last_login_time'],
                        'deleted_at' => 0
                    );

                    DB::table('employee')->insert($employee_data);

                }

                // 页码递增
                $page++;
            }

            return 0;


        }
        catch (\Exception $e) {

            $this->error($e->getMessage());
            return 1;
        }
    }
}
