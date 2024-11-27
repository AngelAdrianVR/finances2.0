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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->float('amount', 10,2)->unsigned();
            $table->float('interest', 10,2)->unsigned();
            $table->float('capital', 10,2)->unsigned();
            $table->float('remainig', 10,2)->unsigned();
            $table->string('payment_method');
            $table->date('date');
            $table->text('notes')->nullable();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
