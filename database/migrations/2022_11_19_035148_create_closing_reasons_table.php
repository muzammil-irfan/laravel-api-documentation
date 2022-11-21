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
        Schema::create('closing_reasons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('enabled');
            $table->timestamps();

            $table->integer('company_id')->unsigned();
        });

        Schema::table("closing_reasons", function($table) {
            $table->foreign('company_id')
                  ->references('id')
                  ->on('companies')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('closing_reasons');
    }
};
