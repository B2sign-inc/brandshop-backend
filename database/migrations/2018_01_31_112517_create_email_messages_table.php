<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('to_name')->nullable();
            $table->string('to_address');
            $table->string('from_name')->nullable();
            $table->string('from_address')->nullable();
            $table->string('subject');
            $table->text('body');
            $table->timestamp('date_sent')->nullable();
            $table->timestamp('date_viewed')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_messages');
    }
}
