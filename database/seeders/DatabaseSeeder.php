<?php

namespace Database\Seeders;

use App\Domain\User\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::unsetEventDispatcher();
        $this->call(UserTypesSeeder::class);
        User::factory(10)
            ->hasWallet(1)
            ->create();
    }
}
