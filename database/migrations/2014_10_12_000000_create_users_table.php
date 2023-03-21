<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('minimum');
            $table->integer('maximum');
            $table->integer('tries');
            $table->integer('seconds');
            $table->integer('guesses')->default(0);
            $table->integer('seconds_to_beat')->default(0);
            $table->integer('answer')->default(0);
            $table->boolean('won')->default(0);
            $table->timestamp('play_date')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};