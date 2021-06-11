<?php

namespace Ranium\SeedOnce\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Ranium\SeedOnce\Repositories\SeederRepositoryInterface as Repository;

class BaseCommand extends Command
{
    /**
     * The connection resolver instance.
     *
     * @var \Illuminate\Database\ConnectionResolverInterface
     */
    protected $resolver;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * Seeder repository
     *
     * @var \Ranium\SeedOnce\Repositories\SeederRepositoryInterface
     */
    protected $repository;

    /**
     * Create a new database seed command instance.
     *
     * @param  \Illuminate\Database\ConnectionResolverInterface  $resolver
     * @param  \Illuminate\Filesystem\Filesystem $files
     * @param  \Illuminate\Container\Container $container
     * @param  \Ranium\SeedOnce\Repositories\SeederRepositoryInterface $repository
     * @return void
     */
    public function __construct(Resolver $resolver,
                                Filesystem $files,
                                Container $container,
                                Repository $repository)
    {
        parent::__construct();

        $this->resolver = $resolver;
        $this->files = $files;
        $this->container = $container;
        $this->repository = $repository;
    }

    /**
     * Get the seeders to mark as seeded.
     * NOTE: Main Database Seeder is always excluded.
     *
     * @param string $classOption Which class to get. "all" for all classes.
     * @return array
     */
    protected function getSeeders($classOption = 'all')
    {
        // Read all files from the database/seeds directory
        return Collection::make($this->laravel->databasePath().DIRECTORY_SEPARATOR. config('seedonce.seeders_folder') .DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) {
                return Str::endsWith($path, '.php') ? [$path] : $this->files->glob($path .'*.php');
            })
            ->map(function ($path) {
                return $this->getSeederName($path);
            })
            ->filter(function ($class) use ($classOption) {
                // Filter out classes based on option passed
                return ($classOption === 'all' || $classOption === $class)
                    // We want to skip DatabaseSeeder as we never mark it as seeded
                    && $class !== config('seedonce.database_seeder');
            });
    }

    /**
     * Get the name of the seeder class.
     *
     * @param  string  $path
     * @return string
     */
    protected function getSeederName($path)
    {
        return str_replace('.php', '', basename($path));
    }

    /**
     * Get the name of the database connection to use.
     *
     * @return string
     */
    protected function getDatabase()
    {
        $database = $this->input->getOption('database');

        return $database ?: $this->laravel['config']['database.default'];
    }
}
