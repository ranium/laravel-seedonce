<?php
namespace Ranium\SeedOnce\Test;

class MarkSeededTest extends TestCase
{
    protected function defineEnvironment($app)
    {
        $app['config']->set('seedonce.folder_seeder', 'seeds');
    }

    /** @test */
    public function it_can_mark_all_seeders_as_seeded()
    {
        $this->artisan('seedonce:mark-seeded')->run();

        $this->assertDatabaseHas('seeders', [
            'seeder' => 'UsersTableSeeder'
        ]);

        $this->assertDatabaseHas('seeders', [
            'seeder' => 'RolesTableSeeder'
        ]);
    }

    /** @test */
    public function it_can_mark_a_seeder_as_seeded()
    {
        $this->artisan('seedonce:mark-seeded', ['--class' => 'UsersTableSeeder'])->run();

        $this->assertDatabaseHas('seeders', [
            'seeder' => 'UsersTableSeeder'
        ]);

        $this->assertDatabaseMissing('seeders', [
            'seeder' => 'RolesTableSeeder'
        ]);
    }
}
