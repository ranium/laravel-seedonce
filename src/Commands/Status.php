<?php

namespace Ranium\SeedOnce\Commands;

use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputOption;

class Status extends BaseCommand
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'seedonce:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the status of each seeder class';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->resolver->setDefaultConnection($this->getDatabase());

        if (! $this->repository->repositoryExists()) {
            $this->error('Seeders table not found. Please run migrate command first.');

            return 1;
        }

        $availableSeeders = $this->getSeeders('all');

        $seeded = $this->repository->getSeeded();

        if (count($classes = $this->getStatusFor($seeded, $availableSeeders)) > 0) {
            $this->table(['Seeded?', 'Seeder'], $classes);
        } else {
            $this->error('No seeders found');
        }
    }

    /**
     * Get the status for the given seeded seeders
     *
     * @param  array  $seeded
     * @param  array  $availableSeeders
     * @return \Illuminate\Support\Collection
     */
    protected function getStatusFor(array $seeded, Collection $availableSeeders)
    {
        $seeded = collect($seeded)->map(function ($_seeded){
            return class_basename($_seeded);
        })->toArray();

        return Collection::make($availableSeeders)
                    ->map(function ($seeder) use ($seeded) {
                        return in_array($seeder, $seeded)
                                ? ['<info>Yes</info>', $seeder]
                                : ['<fg=red>No</fg=red>', $seeder];
                    });
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use'],
        ];
    }
}
