<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ranium\SeedOnce\Traits\SeedOnce;

class RolesTableSeeder extends Seeder
{
    use SeedOnce;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            ['name' => 'admin'],
            ['name' => 'user'],
        ]);
    }
}
