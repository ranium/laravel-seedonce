<?php

namespace Ranium\SeedOnce\Repositories;

interface SeederRepositoryInterface
{
    /**
     * Get the classes already seeded.
     *
     * @return array
     */
    public function getSeeded();

    /**
     * Log that a seeder was run.
     *
     * @param  string  $class
     * @return void
     */
    public function log($class);

    /**
     * Remove a seeder from the log.
     *
     * @param  object  $seeder
     * @return void
     */
    public function delete($seeder);

    /**
     * Determine if the seeder repository exists.
     *
     * @return bool
     */
    public function repositoryExists();

}
