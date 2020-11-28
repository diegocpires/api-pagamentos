<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->decimal('value', 14, 2, true);
            $table->unsignedBigInteger('payer')->unsigned();
            $table->unsignedBigInteger('payee')->unsigned();
            $table->string('status', 1);
            $table->timestamps();
            $table->foreign('payer')->references('id')->on('customers');
            $table->foreign('payee')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
