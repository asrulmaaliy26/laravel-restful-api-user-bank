<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBalancesTable extends Migration
{
    public function up()
    {
        Schema::create('balances', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable(false);
            $table->char('pin',6);
            $table->integer('amount')->default(0);
            $table->string('history')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->timestamps();

            $table->foreign('user_id')->on("users")->references("id");
        });
    }

    public function down()
    {
        Schema::dropIfExists('balances');
    }
}
