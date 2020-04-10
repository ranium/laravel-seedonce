<?php

namespace Ranium\SeedOnce\Traits;

use Ranium\SeedOnce\Repositories\SeederRepositoryInterface;

trait SeedOnce {

    /**
     * Seed the given connection from the given path.
     *
     * @param  array|string  $class
     * @param  bool  $silent
     * @return $this
     */
    public function call($class, $silent = false)
    {
        if ($this->hasSeeded($class)) {
            if ($silent === false && isset($this->command)) {
                $this->command->getOutput()->writeln("<info>Skipped:</info> {$class}");
            }
            return $this;
        }

        parent::call($class, $silent);

        return $this;
    }

    /**
     * Run the database seeds.
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function __invoke()
    {
        $class = get_class($this);

        if ($this->hasSeeded($class)) {
            return;
        }

        $return = parent::__invoke();

        $this->markSeeded($class);

        return $return;
    }

    /**
     * Determine if this seeder class has already been seeded or not.
     *
     * @param  string $class
     * @return boolean
     */
    protected function hasSeeded($class)
    {
        $seeded = $this->repository()->getSeeded();

        // Check if current class is already seeded
        return in_array($class, $seeded);
    }

    /**
     * Mark the current class as seeded
     *
     * @param  string $class
     * @return void
     */
    protected function markSeeded($class)
    {
        // We will mark class as seeded only if it is not DatabaseSeeder class.
        // We are assuming that DatabaseSeeder never seeds directly and 
        // always calls other seeder classes to seed.
        if ($class !== 'DatabaseSeeder') {
            $this->repository()->log($class);
        }
    }

    /**
     * Get the instance of seeder repository
     *
     * @return \Ranium\SeedOnce\Repositories\SeederRepositoryInterface
     */
    protected function repository()
    {
        return resolve(SeederRepositoryInterface::class);
    }
}
