<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_tokens', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('user_agent');
            $table->string('ip');
            $table->string('firebase_id')->default('');
            $table->mediumText('token');
            $table->dateTime('expire')->default(date('Y-m-d H:i:s', strtotime('+ 1 day')));
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('user_tokens');
    }
}
