<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('outcomes', function (Blueprint $table) {
            // Enlaza los gastos generados automaticamente con el gasto compartido que los origino.
            // Permite reembolsar el saldo de cada usuario vinculado de forma exacta al editar o eliminar.
            $table->foreignId('parent_outcome_id')
                ->nullable()
                ->after('user_id')
                ->constrained('outcomes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('outcomes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_outcome_id');
        });
    }
};
