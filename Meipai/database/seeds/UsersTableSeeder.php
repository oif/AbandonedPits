<?php

# database/seeds/QuoteTableSeeder.php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        for ($i = 0; $i < DatabaseSeeder::USER_COUNT; ++$i) {
            $this->addUser();
        }
        for ($i = 1; $i <= DatabaseSeeder::USER_COUNT; $i++) {
            $user = User::find($i);
            $fo = rand(0, DatabaseSeeder::MAX_FOLLOW);
            for ($j = 0; $j < $fo; $j++) {
                $rd = rand(1, DatabaseSeeder::USER_COUNT);
                while (true) {
                    if ($rd != $i) {
                        break;
                    } else {
                        $rd = rand(1, DatabaseSeeder::USER_COUNT);
                    }
                }
                $target = User::findOrFail($rd);
                $user->followQueue($target);
            }
        }
    }

    private function addUser()
    {
        return $user = User::create(array(
            'name' => DatabaseSeeder::randString(7),
        ));
    }
}
