<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('ruc')->unique();
            $table->string('user_id')->nullable();

            $table->string('department');
            $table->string('city');
            $table->string('phone');
            $table->string('address');
            $table->string('site');

            $table->string('logo');
            $table->text('description');
            $table->boolean('enabled');
            $table->timestamps();

            $table->integer('country_id')->unsigned();
            $table->integer('business_id')->unsigned();
            $table->integer('sector_id')->unsigned();
            
                  
        });

        Schema::table("companies",function($table) {
            $table->foreign('country_id')
            ->references('id')
            ->on('countries');
            $table->foreign('business_id')
                  ->references('id')
                  ->on('businesses');
            $table->foreign('sector_id')
                  ->references('id')
                  ->on('sectors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
};
