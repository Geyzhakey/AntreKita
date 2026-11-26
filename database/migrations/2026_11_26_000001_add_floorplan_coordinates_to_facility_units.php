<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->boolean('has_custom_floorplan')->default(false)->after('map_rows');
        });

        Schema::table('facility_units', function (Blueprint $table) {
            $table->unsignedTinyInteger('floor')->default(1)->after('zone');
            $table->string('seat_type', 20)->nullable()->after('floor');
            $table->string('display_label', 40)->nullable()->after('name');
            $table->decimal('map_x', 6, 2)->nullable()->after('pos_y');
            $table->decimal('map_y', 6, 2)->nullable()->after('map_x');
            $table->decimal('map_w', 6, 2)->nullable()->after('map_y');
            $table->decimal('map_h', 6, 2)->nullable()->after('map_w');
        });
    }

    public function down(): void
    {
        Schema::table('facility_units', function (Blueprint $table) {
            $table->dropColumn([
                'floor',
                'seat_type',
                'display_label',
                'map_x',
                'map_y',
                'map_w',
                'map_h',
            ]);
        });

        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn('has_custom_floorplan');
        });
    }
};
