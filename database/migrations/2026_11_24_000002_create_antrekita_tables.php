<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_whitelists', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('institution_name');
            $table->string('status', 20)->default('pending'); // pending | approved | revoked
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('city');
            $table->string('address');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('facility_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
            $table->string('code', 32);
            $table->string('name');
            $table->string('zone', 32); // group_room | indoor_desk | outdoor_desk
            $table->unsignedSmallInteger('capacity')->default(1);
            $table->unsignedSmallInteger('max_duration_minutes')->default(180);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['institution_id', 'code']);
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
            $table->foreignId('facility_unit_id')->constrained('facility_units')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('group_name');
            $table->unsignedSmallInteger('people_count')->default(1);
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('status', 20)->default('active'); // active | waiting | completed | cancelled
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['facility_unit_id', 'status']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('channel', 20); // whatsapp | email
            $table->string('recipient');
            $table->string('trigger_type', 40); // booking_confirmed | h_minus_5_active | h_minus_5_waiting | handover_ready
            $table->text('message');
            $table->timestamp('sent_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('facility_units');
        Schema::dropIfExists('institutions');
        Schema::dropIfExists('institution_whitelists');
    }
};
