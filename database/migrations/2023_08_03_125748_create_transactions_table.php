<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->uuid('uuid');
            $table->unsignedBigInteger('payer');
            $table->foreign("payer", 'payer_fk')->references("id")->on("users");
            $table->unsignedBigInteger('payee');
            $table->foreign("payee", 'payee_fk')->references("id")->on("users");
            $table->decimal('value', 10, 2);
            $table->enum('status', ['pending', 'completed', 'canceled'])->default('completed');
            $table->enum('type', ['transfer', 'deposit', 'withdraw'])->default('transfer');
            $table->string('description')->nullable();
            $table->string('authorization_code')->nullable();
            $table->boolean('was_notified')->nullable();
            $table->dateTime('was_notified_at')->nullable();
            $table->string('was_reversed')->nullable();
            $table->dateTime('was_reversed_at')->nullable();
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
        Schema::dropIfExists('transactions');
    }
};
