<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeedersTable extends Migration
{
    /**
     * Seeders table name
     *
     * @var string
     */
    protected $tableName;

    /**
     * Set the table name in the constructor
     * 
     * @return void
     */
    public function __construct()
    {
        $this->tableName = config('seedonce.table', 'seeders');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable($this->tableName)) {
            Schema::create($this->tableName, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('seeder');
                $table->timestamp('seeded_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
