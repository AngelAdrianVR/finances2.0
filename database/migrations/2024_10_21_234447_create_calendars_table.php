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
        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('title');
            $table->date('date')->nullable();
            $table->float('amount')->unsigned();
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->text('periodicity')->nullable(); //frecuencia con la que se paga o se recibe el dinero
            $table->string('payment_method')->nullable();
            $table->string('status')->default('Pendiente');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // $table->string('end_time', 10)->nullable();
            // $table->string('start_time', 10)->nullable();
            // $table->date('finish_date')->nullable();
            // $table->boolean('is_full_day')->default(false);
            // $table->string('reminder')->nullable();
            // $table->string('repeater')->nullable();
            // $table->json('participants')->nullable();
            // $table->text('location')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendars');
    }
};
