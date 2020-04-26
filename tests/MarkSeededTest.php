<?php
namespace Ranium\SeedOnce\Test;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ranium\SeedOnce\Repositories\SeederRepositoryInterface;

class MarkSeededTest extends TestCase
{
    /**
     * Set up test case
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
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
