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
        Schema::create('denouncements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_in_hash', 32)->unique();

            $table->string('state', 32);
            $table->string('priority', 16)->nullable();
            $table->text('description');
            $table->text('people');
            $table->timestamps();

            $table->integer('investigator_id')->nullable();

            $table->integer('company_id')->unsigned();
            $table->integer('area_id')->unsigned();
            $table->integer('source_id')->unsigned();
            $table->integer('informer_id')->unsigned();
            $table->integer('office_id')->unsigned();
            $table->integer('category_id')->unsigned();

            $table->integer('closing_reason_id')->nullable();
            $table->text('closing_description')->nullable();
        });

        Schema::table("denouncements", function ($table) {
            $table->foreign('company_id')
                ->references('id')
                ->on('companies');
            $table->foreign('area_id')
                ->references('id')
                ->on('areas');
            $table->foreign('source_id')
                ->references('id')
                ->on('sources');
            $table->foreign('informer_id')
                ->references('id')
                ->on('informers');
            $table->foreign('office_id')
                ->references('id')
                ->on('offices');
            $table->foreign('category_id')
                ->references('id')
                ->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('denouncements');
    }
};
