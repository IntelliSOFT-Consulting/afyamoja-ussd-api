<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sessionId', 200);
            $table->string('access_token', 200);
            $table->string('phonenumber', 20);
            $table->integer('level');
            $table->mediumText('text');
            $table->mediumText('choice', 250);
            $table->integer('provider');
            $table->string('dependent', 100);
            $table->integer('dependent_age');
            $table->string('kin', 100);
            $table->boolean('forgot_password')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sessions');
    }
}
