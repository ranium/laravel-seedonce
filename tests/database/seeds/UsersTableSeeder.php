<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ranium\SeedOnce\Traits\SeedOnce;

class UsersTableSeeder extends Seeder
{
    use SeedOnce;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Test User',
            'email' => 'test@ranium.in',
            'password' => bcrypt('password')
        ]);
    }
}
