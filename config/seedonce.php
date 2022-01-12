<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Table Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of the table that will hold the details of the
    | seeders that have been executed.
    |
    */
    'table' => 'seeders',

    /*
    |--------------------------------------------------------------------------
    | Main Database Seeder class name
    |--------------------------------------------------------------------------
    |
    | This value is the name of the class that seeds all other seeders.
    | In most of the cases this will be DatabaseSeeder.
    |
    */
    'database_seeder' => 'DatabaseSeeder',



    /*
    |--------------------------------------------------------------------------
    | Folder Name
    |--------------------------------------------------------------------------
    |
    | This value belongs to the folder where the seeders are housed
    |
    */
    'folder_seeder' => (float) app()->version() >= 8 ? 'seeders' : 'seeds',
];
