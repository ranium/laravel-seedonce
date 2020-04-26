<?php
namespace Ranium\SeedOnce\Test;

use Ranium\SeedOnce\SeedOnceServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Ranium\SeedOnce\Repositories\SeederRepositoryInterface;

class TestCase extends BaseTestCase
{
    /**
     * Set up test case
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->setupDb();
    }

    /**
     * Load the package service providers
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getPackageProviders($app)
    {
        return [
            SeedOnceServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app->useDatabasePath(__DIR__ . '/database');
    }

    /**
     * Setup database for the tests
     *
     * @return void
     */
    protected function setupDb()
    {
        $this->loadLaravelMigrations();
        $this->loadPackageMigrations();
    }

    /**
     * Run migrations from this package
     *
     * @return void
     */
    protected function loadPackageMigrations()
    {
        // This runs the migrations included in the seedonce package
        $this->artisan('migrate')->run();

        // Load migrations needed for testing this package
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }
}
