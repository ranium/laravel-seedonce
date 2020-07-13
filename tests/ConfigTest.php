<?php
namespace Ranium\SeedOnce\Test;

use Illuminate\Support\Facades\Schema;

class ConfigTest extends TestCase
{
    /**
     * Define environment setup for custom table
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function useCustomTable($app)
    {
        $app->config->set('seedonce.table', 'my_seeders');
    }

    /**
     * Define environment setup for custom database seeder
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function useCustomDatabaseSeeder($app)
    {
        $app->config->set('seedonce.database_seeder', 'MyDatabaseSeeder');
    }

    /**
     * @test
     * @environment-setup useCustomTable
     */
    public function it_can_have_seeder_table_name()
    {
        $table = 'my_seeders';

        $this->assertTrue(Schema::hasTable($table));

        $this->artisan('db:seed')->run();

        $this->assertDatabaseHas($table, [
            'seeder' => 'UsersTableSeeder'
        ]);

        $this->assertDatabaseHas($table, [
            'seeder' => 'RolesTableSeeder'
        ]);
    }

    /**
     * @test
     * @environment-setup useCustomDatabaseSeeder
     */
    public function it_can_have_database_seeder_name()
    {
        $this->artisan('db:seed')->run();

        // Since we changed the name of database_seeder (in useCustomDatabaseSeeder),
        // the seeders table should have the default DatabaseSeeder marked as seeded.
        $this->assertDatabaseHas('seeders', [
            'seeder' => 'DatabaseSeeder'
        ]);
    }
}
