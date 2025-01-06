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
        Schema::create('bank_cards', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //nombre o alias de la tarjeta para que el usuario la identifique
            $table->string('owner_name')->nullable();
            $table->string('bank_name');
            $table->string('type')->nullable();
            $table->string('notes')->nullable();
            $table->string('is_active')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_cards');
    }
};
