<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('payment_periodicity')->nullable(); //cada cuanto va a pagar al que le prestaste o tu a quien te prestó
            $table->string('profitability_type')->nullable(); //tipo de interes: simple, compuesto, fijo, amortizable
            $table->string('profitability_mode')->nullable(); //porcentaje o monto plano
            $table->string('beneficiary_name')->nullable(); //nombre de a quién prestaste
            $table->string('lender_name')->nullable(); //nombre del prestamista que te prestó
            $table->timestamp('loan_date')->nullable(); //fecha del préstamo
            $table->float('amount')->unsigned();
            $table->boolean('automatic')->default(true); //bandera que indica si al registrar abono se registra automaticamente ingreso / gasto
            $table->boolean('is_for_me')->default(false); //bandera que indica si el prestamo es para mi (yo lo recibí)
            $table->float('profitability')->unsigned()->nullable();
            $table->string('profitability_period')->nullable();
            $table->timestamp('expired_date')->nullable();
            $table->string('status');
            $table->string('description')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
