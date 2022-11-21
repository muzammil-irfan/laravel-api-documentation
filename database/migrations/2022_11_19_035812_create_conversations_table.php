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
        Schema::create('conversations', function (Blueprint $table) {
            $table->increments('id');
            $table->text('message');
            $table->timestamps();
            $table->boolean('informer_can_see')->default(false);
            $table->integer('user_id')->nullable();
            $table->integer('informer_id')->nullable();

            $table->integer('denounces_id')->unsigned();
        });

        Schema::table("conversations",function($table) {
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
        Schema::dropIfExists('conversations');
    }
};
