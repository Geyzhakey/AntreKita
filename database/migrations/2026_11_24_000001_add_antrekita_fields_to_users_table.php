<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('whatsapp_number', 30)->nullable()->unique()->after('email');
            $table->string('role', 20)->default('visitor')->after('whatsapp_number');
            $table->boolean('is_verified_admin')->default(false)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['whatsapp_number']);
            $table->dropColumn(['whatsapp_number', 'role', 'is_verified_admin']);
        });
    }
};
