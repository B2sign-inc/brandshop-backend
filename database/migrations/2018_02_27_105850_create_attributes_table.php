<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('identifier')->unique();
            $table->enum('data_type',['INTEGER','DECIMAL','DATETIME','VARCHAR','BOOLEAN','TEXT'])->nullable()->default(null);
            $table->enum('field_type', ['TEXT', 'TEXTAREA', 'CKEDITOR', 'SELECT', 'FILE', 'DATETIME','CHECKBOX','RADIO','SWITCH']);
            $table->integer('sort_order')->nullable()->default(0);
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
        Schema::dropIfExists('attributes');
    }
}
