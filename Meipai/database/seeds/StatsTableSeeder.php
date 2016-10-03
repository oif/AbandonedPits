<?php

# database/seeds/QuoteTableSeeder.php

use App\Stat;
use Illuminate\Database\Seeder;

class StatsTableSeeder extends Seeder
{
    public function run()
    {
        for ($i = 0; $i < DatabaseSeeder::STAT_COUNT; ++$i) {
            $this->addStat();
        }
    }

    private function addStat()
    {
        Stat::create(array(
            'user_id' => rand(1, DatabaseSeeder::USER_COUNT),
            'content' => DatabaseSeeder::randString(DatabaseSeeder::STAT_LENGTH, true),
        ));
    }
}
