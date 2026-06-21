<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add unique link_code to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('link_code', 12)->nullable()->unique()->after('email');
        });

        // Create user_links pivot table
        Schema::create('user_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('linked_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('accepted');
            $table->timestamps();

            $table->unique(['user_id', 'linked_user_id']);
        });

        // Add split columns to outcomes table
        Schema::table('outcomes', function (Blueprint $table) {
            $table->boolean('split_enabled')->default(false)->after('amount');
            $table->json('split_with')->nullable()->after('split_enabled');
        });

        // Generate link codes for existing users
        $users = \App\Models\User::whereNull('link_code')->get();
        foreach ($users as $user) {
            $user->link_code = \App\Models\User::generateUniqueLinkCode();
            $user->save();
        }
    }

    public function down(): void
    {
        Schema::table('outcomes', function (Blueprint $table) {
            $table->dropColumn(['split_enabled', 'split_with']);
        });

        Schema::dropIfExists('user_links');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('link_code');
        });
    }
};
