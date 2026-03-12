<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogAcessoTable extends Migration
{
    public function up()
    {
        Schema::create('log_acesso', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_acesso');
    }
}
