<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 60)->nullable()->unique()->after('name');
            $table->string('affiliation', 150)->nullable()->after('username');
            $table->string('registration_code', 40)->nullable()->unique()->after('role');
            $table->string('institution_label', 150)->nullable()->after('registration_code');
            $table->text('purpose')->nullable()->after('institution_label');
            $table->boolean('is_frozen')->default(false)->after('is_verified_admin');
            $table->string('frozen_reason', 255)->nullable()->after('is_frozen');
            $table->timestamp('frozen_at')->nullable()->after('frozen_reason');
        });

        Schema::table('institutions', function (Blueprint $table) {
            $table->string('registration_code', 40)->nullable()->unique()->after('slug');
            $table->string('building_type', 80)->default('Perpustakaan')->after('city');
            $table->text('purpose')->nullable()->after('description');
            $table->unsignedTinyInteger('map_cols')->default(6)->after('is_active');
            $table->unsignedTinyInteger('map_rows')->default(4)->after('map_cols');
        });

        Schema::table('facility_units', function (Blueprint $table) {
            $table->unsignedTinyInteger('pos_x')->default(1)->after('sort_order');
            $table->unsignedTinyInteger('pos_y')->default(1)->after('pos_x');
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('developer_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained('users')->cascadeOnDelete();
            $table->string('action', 60);
            $table->string('target_label', 180);
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('developer_audit_logs');
        Schema::dropIfExists('system_settings');

        Schema::table('facility_units', function (Blueprint $table) {
            $table->dropColumn(['pos_x', 'pos_y']);
        });

        Schema::table('institutions', function (Blueprint $table) {
            $table->dropUnique(['registration_code']);
            $table->dropColumn(['registration_code', 'building_type', 'purpose', 'map_cols', 'map_rows']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropUnique(['registration_code']);
            $table->dropColumn([
                'username',
                'affiliation',
                'registration_code',
                'institution_label',
                'purpose',
                'is_frozen',
                'frozen_reason',
                'frozen_at',
            ]);
        });
    }
};
