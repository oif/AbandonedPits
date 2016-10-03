<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    const USER_COUNT = 2000;
    const STAT_COUNT = 70000;
    const STAT_LENGTH = 20;
    const MAX_FOLLOW = 20;

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->call('UsersTableSeeder');
        $this->call('StatsTableSeeder');
    }

    /**
     * 生成随机字符串.
     *
     * @param int    $length       字符串长度
     * @param string $specialChars 是否有特殊字符
     *
     * @return string
     */
    public static function randString($length, $specialChars = false)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        if ($specialChars) {
            $chars .= '!@#$%^&*()';
        }

        $result = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; ++$i) {
            $result .= $chars[rand(0, $max)];
        }

        return $result;
    }
}
