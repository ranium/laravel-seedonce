<?php

namespace Ranium\SeedOnce\Commands;

use Illuminate\Database\Seeder;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Input\InputOption;


class MarkSeeded extends BaseCommand
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

        if (! $this->repository->repositoryExists()) {
            $this->error('Seeders table not found. Please run migrate command first.');

            return 1;
        }

        $seeders = $this->getSeeders($this->option('class'));

        $this->info(count($seeders) . ' seeder(s) will be marked as seeded');

        $this->mark($seeders);

        $this->info('Completed successfully.');
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
