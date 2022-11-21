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
        Schema::create('evidences', function (Blueprint $table) {
            $table->increments('id');
            $table->string('resource_url');
            $table->boolean('is_primary');
            $table->integer('conversation_id')->nullable();
            
            $table->timestamps();

            $table->integer('denounces_id')->unsigned();
        });

        Schema::table("evidences", function ($table) {
            $table->foreign('denounces_id')
                  ->references('id')
                  ->on('denouncements')
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
        Schema::dropIfExists('evidences');
    }
};
