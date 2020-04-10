<?php

namespace Ranium\SeedOnce\Traits;

use Ranium\SeedOnce\Repositories\SeederRepositoryInterface;

trait SeedOnce {
    /**
     * Run the database seeds.
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function __invoke()
    {
        if ($this->hasSeeded()) {
            return;
        }

        $return = parent::__invoke();

        $this->markSeeded();

        return $return;
    }

    /**
     * Determine if this seeder class has already been seeded or not.
     *
     * @return boolean
     */
    protected function hasSeeded()
    {
        $seeded = $this->repository()->getSeeded();

        // Check if current class is already seeded
        return in_array(get_class(), $seeded);
    }

    /**
     * Mark the current class as seeded
     *
     * @return void
     */
    protected function markSeeded()
    {
        $this->repository()->log(get_class());
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
