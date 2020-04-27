<?php
namespace Ranium\SeedOnce\Test;

use Illuminate\Support\Facades\Schema;

class ConfigTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('seedonce.table', 'my_seeders');
        parent::getEnvironmentSetUp($app);
    }

    /** @test */
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
}
