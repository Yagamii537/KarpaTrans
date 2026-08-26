<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subclients', function (Blueprint $table) {

            $table->boolean('inherits_operational_rules')
                ->default(true)
                ->after('address');

            $table->unsignedInteger('free_loading_hours')
                ->nullable()
                ->after('inherits_operational_rules');

            $table->unsignedInteger('free_unloading_hours')
                ->nullable()
                ->after('free_loading_hours');

            $table->enum('service_time_start', [
                'requested_time',
                'arrival_time',
            ])
                ->nullable()
                ->after('free_unloading_hours');

            $table->unsignedInteger('standby_fraction_minutes')
                ->nullable()
                ->after('service_time_start');
        });
    }

    public function down(): void
    {
        Schema::table('subclients', function (Blueprint $table) {

            $table->dropColumn([
                'inherits_operational_rules',
                'free_loading_hours',
                'free_unloading_hours',
                'service_time_start',
                'standby_fraction_minutes',
            ]);
        });
    }
};
