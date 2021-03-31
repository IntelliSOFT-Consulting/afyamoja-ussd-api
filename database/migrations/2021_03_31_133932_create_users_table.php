<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('phonenumber', 25);
            $table->string('first_name', 15);
            $table->string('last_name', 15);
            $table->string('dob', 10);
            $table->string('gender', 10);
            $table->integer('id_number', 25);
            $table->mediumText('pin');
            $table->boolean('terms_conditions')->default(0);
            $table->boolean('terms_conditions_sent')->default(0);
            $table->boolean('feedback_consent')->default(0);
            $table->boolean('feedback_sent')->default(0);
            $table->boolean('status')->default(0);
            $table->boolean('isSynced')->default(0);
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
        Schema::dropIfExists('users');
    }
}
