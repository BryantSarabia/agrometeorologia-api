<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function(Blueprint $table){
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('SET NULL');
        });

        Schema::table('requests', function(Blueprint $table){
            $table->foreign('project_id')->references('id')
               ->on('projects')->onDelete('NO ACTION');
        });

        Schema::table('requests', function(Blueprint $table){
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('CASCADE');
        });

        Schema::table('reports', function(Blueprint $table){
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('NO ACTION');
        });

        Schema::table('locations', function(Blueprint $table){
           $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
