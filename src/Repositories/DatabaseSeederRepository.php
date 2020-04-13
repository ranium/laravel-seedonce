<?php

namespace Ranium\SeedOnce\Repositories;

use Illuminate\Database\ConnectionResolverInterface as Resolver;

class DatabaseSeederRepository implements SeederRepositoryInterface
{
    /**
     * The database connection resolver instance.
     *
     * @var \Illuminate\Database\ConnectionResolverInterface
     */
    protected $resolver;

    /**
     * The name of the seeder table.
     *
     * @var string
     */
    protected $table;

    /**
     * Array of classes that are seeded
     *
     * @var array
     */
    protected static $seeded = null;

    /**
     * Create a new database seeder repository instance.
     *
     * @param  \Illuminate\Database\ConnectionResolverInterface  $resolver
     * @param  string  $table
     * @return void
     */
    public function __construct(Resolver $resolver, $table)
    {
        $this->table = $table;
        $this->resolver = $resolver;
    }

    /**
     * Get all seeded classes
     *
     * @return array
     */
    public function getSeeded()
    {
        if (self::$seeded === null) {
            self::$seeded = $this->table()
                    ->orderBy('seeded_at', 'asc')
                    ->pluck('seeder')->all();
        }

        return self::$seeded;
    }

    /**
     * Log that a seeder was run.
     *
     * @param  string  $class
     * @return void
     */
    public function log($class)
    {
        $record = ['seeder' => $class, 'seeded_at' => now()];

        $this->table()->insert($record);

        // If getSeeded() was not called in current request,
        // then no need to push this class in the $seeded array.
        // We will fetch fresh data from the db in that case.
        if (self::$seeded !== null) {
            self::$seeded[] = $class;
        }
    }

    /**
     * Remove a seeder from the log.
     *
     * @param  object  $seeder
     * @return void
     */
    public function delete($seeder)
    {
        $this->table()->where('seeder', $seeder->seeder)->delete();
    }

    /**
     * Get a query builder for the seeder table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function table()
    {
        return $this->getConnection()->table($this->table)->useWritePdo();
    }

    /**
     * Resolve the database connection instance.
     *
     * @return \Illuminate\Database\Connection
     */
    public function getConnection()
    {
        return $this->resolver->connection();
    }

    /**
     * Determine if the seeder repository exists.
     *
     * @return bool
     */
    public function repositoryExists()
    {
        $schema = $this->getConnection()->getSchemaBuilder();

        return $schema->hasTable($this->table);
    }
}
