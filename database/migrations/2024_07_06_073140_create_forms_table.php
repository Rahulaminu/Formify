<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormsTable extends Migration
{
    public function up()
{
    Schema::create('forms', function (Blueprint $table) {
        $table->id();
        $table->foreignId('creator_id')->constrained('users');
        $table->string('name');
        $table->string('slug')->unique();
        $table->json('allowed_domains');
        $table->text('description')->nullable();
        $table->boolean('limit_one_response')->default(false);
        $table->timestamps();
    });
}


    public function down()
    {
        Schema::dropIfExists('forms');
    }
}
