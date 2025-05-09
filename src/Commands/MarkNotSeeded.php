<?php

namespace Ranium\SeedOnce\Commands;

use Illuminate\Database\Seeder;
use Illuminate\Console\ConfirmableTrait;
use stdClass;
use Symfony\Component\Console\Input\InputOption;


class MarkNotSeeded extends BaseCommand
{
    use ConfirmableTrait;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'seedonce:mark-notseeded';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark the given class (or all) as not seeded';

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

        $this->info(count($seeders) . ' seeder(s) will be marked as not seeded');

        $this->unmark($seeders);

        $this->info('Completed successfully.');
    }

    /**
     * UnMark/Delete the seeder class
     *
     * @param array $seeders
     * @return void
     */
    protected function unmark($seeders)
    {
        $seeded = $this->repository->getSeeded();
        $seederObject =  new stdClass;
        foreach ($seeders as $class) {
            $seeder = $this->container->make($class);
            $this->getOutput()->writeln('<info>Un-Marking: </info>' . $class);
            if ($seeder instanceof Seeder && in_array($class, $seeded)) {
                $seederObject->seeder = $class;
                $this->repository->delete($seederObject);
                $this->getOutput()->writeln('<comment>UnMarked: </comment>' . $class);
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
