<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('outcomes', function (Blueprint $table) {
            $table->boolean('is_credit')->default(false)->after('payment_method');
            $table->date('payment_due_date')->nullable()->after('is_credit');
            $table->date('reminder_last_sent_date')->nullable()->after('payment_due_date');
        });
    }

    public function down(): void
    {
        Schema::table('outcomes', function (Blueprint $table) {
            $table->dropColumn(['is_credit', 'payment_due_date', 'reminder_last_sent_date']);
        });
    }
};
