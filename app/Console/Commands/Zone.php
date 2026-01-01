<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Zone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:zone';

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
//        echo "工作目录: " . getcwd();
        self::convert();
    }

    //获取中国地区的json数据
    private function getProvince()
    {
        $filePath ='./public/china.json';

        if (!file_exists($filePath)) {
            throw new \Exception('china.json文件不存在');
        }

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON解析错误: ' . json_last_error_msg());
        }

        $provinces = [];
        foreach ($data['features'] as $item) {
            $provinces[] = $item['properties']['name'];
        }

        return $data;
    }

    //获取全球国家数据
    private function getCountries(){

        $filePath ='./public/world.json';

        if (!file_exists($filePath)) {
            throw new \Exception('world.json文件不存在');
        }

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON解析错误: ' . json_last_error_msg());
        }

        $countries = [];
        foreach ($data['features'] as $item) {
            $countries[] = $item['properties']['name'];
        }
        print_r($countries);exit();
        return $countries;

    }

    //
    private function convert()
    {
        try {
            // 从if_country_iso表读取数据
            $countries = DB::table('country_iso')->get();

            foreach ($countries as $country) {
                // 检查国家是否已存在
                $exists = DB::table('country')
                    ->where('code', $country->code)
                    ->exists();

                if (!$exists) {
                    // 插入新记录
                    $country_id = DB::table('country')->insertGetId([
                        'code' => $country->code,
                        'address_format' => $country->address_format,
                        'status' => 1,
                        'created_by' => 0,
                        'updated_by' => 0,
                        'created_at' => time(),
                        'updated_at' => time()
                    ]);

                    DB::table('country_description')->insert([
                        ['country_id' => $country_id, 'language_id' => 1,'name'=>$country->name_cn],
                        ['country_id' => $country_id, 'language_id' => 2,'name'=>$country->name_en],
                    ]);
                }
            }

            $this->info('数据转换完成');
        } catch (\Exception $e) {
            $this->error('数据转换失败: ' . $e->getMessage());
        }
    }
}
