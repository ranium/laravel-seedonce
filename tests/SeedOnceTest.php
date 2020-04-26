<?php
namespace Ranium\SeedOnce\Test;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ranium\SeedOnce\Repositories\SeederRepositoryInterface;

class SeedOnceTest extends TestCase
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
    public function it_migrates_seeders_table()
    {
        $this->loadPackageMigrations();
        $this->assertTrue(Schema::hasTable('seeders'));
    }

    /** @test */
    public function it_stores_seeders()
    {
        $this->loadPackageMigrations();

        $this->artisan('db:seed')->run();

        $this->assertDatabaseHas('seeders', [
            'seeder' => 'UsersTableSeeder'
        ]);
    }

    /** @test */
    public function it_runs_seeders_only_once()
    {
        $this->loadPackageMigrations();

        $this->artisan('db:seed')->run();

        $this->artisan('db:seed')->run();

        // Find the count of rows in users table. It should be 1
        $this->assertEquals(DB::table('users')->count(), 1);
        $this->assertEquals(DB::table('roles')->count(), 2);
    }
}
