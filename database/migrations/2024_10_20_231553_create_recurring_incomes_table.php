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
        Schema::create('recurring_incomes', function (Blueprint $table) {
            $table->id();
            $table->string('concept');
            $table->boolean('is_active')->default(true); //activo ingreso recurrente
            $table->string('periodicity')->nullable(); //periodo de recurrencia: semanal, mensual...
            $table->float('amount')->unsigned();
            $table->string('payment_method')->nullable();
            $table->string('description')->nullable();
            $table->string('category')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_incomes');
    }
};
