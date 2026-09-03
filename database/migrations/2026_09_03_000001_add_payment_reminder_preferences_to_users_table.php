<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Recordatorios de gastos a crédito (activados por defecto para todos)
            $table->boolean('payment_reminder_enabled')->default(true)->after('link_code');
            $table->unsignedTinyInteger('payment_reminder_days_before')->default(2)->after('payment_reminder_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['payment_reminder_enabled', 'payment_reminder_days_before']);
        });
    }
};
