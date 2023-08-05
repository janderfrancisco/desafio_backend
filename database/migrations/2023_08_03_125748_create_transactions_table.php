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
            $table->unsignedBigInteger('payer_id');
            $table->foreign("payer_id", 'payer_id_fk')->references("id")->on("users");
            $table->unsignedBigInteger('payee_id');
            $table->foreign("payee_id", 'payee_id_fk')->references("id")->on("users");
            $table->decimal('value', 10, 2);
            $table->enum('status', ['pending', 'completed', 'canceled'])->default('pending');
            $table->enum('type', ['transfer', 'deposit', 'withdraw'])->default('transfer');
            $table->string('description')->nullable();
            $table->string('authorization_code')->nullable();
            $table->string('was_notified')->nullable();
            $table->string('was_notified_at')->nullable();
            $table->string('was_reversed')->nullable();
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
