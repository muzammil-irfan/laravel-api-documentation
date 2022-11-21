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
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('readed');
            $table->timestamps();

            $table->integer('user_id')->unsigned();


            $table->string('title');
            $table->string('type');
            $table->string('description');

            $table->integer('denounces_id')->unsigned();
        });

        Schema::table("notifications", function ($table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
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
        Schema::dropIfExists('notifications');
    }
};
