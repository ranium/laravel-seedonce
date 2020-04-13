<?php

namespace Ranium\SeedOnce\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Ranium\SeedOnce\Repositories\SeederRepositoryInterface as Repository;

class MarkSeeded extends Command
{
    use ConfirmableTrait;
    
    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'seedonce:mark-seeded';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark the given class (or all) as seeded';

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
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $this->resolver->setDefaultConnection($this->getDatabase());
        
        $seeders = $this->getSeeders();

        $this->info(count($seeders) . ' seeder(s) will be marked as seeded');

        $this->mark($seeders);

        $this->info('Completed successfully.');
    }

    /**
     * Get the seeders to mark as seeded
     *
     * @return array
     */
    protected function getSeeders()
    {
        // Read all files from the database/seeds directory
        return Collection::make($this->laravel->databasePath().DIRECTORY_SEPARATOR.'seeds'.DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) {
                return Str::endsWith($path, '.php') ? [$path] : $this->files->glob($path .'*.php');
            })
            ->map(function ($path) {
                return $this->getSeederName($path);
            })
            ->filter(function ($class) {
                // Filter out classes based on option passed
                return ($this->option('class') === 'all' || $this->option('class') === $class)
                    // We want to skip DatabaseSeeder as we never mark it as seeded
                    && $class !== 'DatabaseSeeder';
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
     * Mark/Log the seeder class
     *
     * @param array $seeders
     * @return void
     */
    protected function mark($seeders)
    {
        $seeded = $this->repository->getSeeded();
        
        foreach ($seeders as $class) {
            $seeder = $this->container->make($class);
            $this->getOutput()->writeln('<info>Marking: </info>' . $class);
            if ($seeder instanceof Seeder && ! in_array($class, $seeded)) {
                $this->repository->log($class);
                $this->getOutput()->writeln('<comment>Marked: </comment>' . $class);
            } else {
                $this->getOutput()->writeln('<comment>Skipped: </comment>' . $class);
            }
        }
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

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['class', null, InputOption::VALUE_OPTIONAL, 'The name of the seeder class to mark as seeded', 'all'],

            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use'],

            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],
        ];
    }
}
