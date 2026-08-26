<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trip_times', function (Blueprint $table) {
            $table->unique(
                ['trip_id', 'event_type'],
                'trip_times_trip_event_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('trip_times', function (Blueprint $table) {
            $table->dropUnique('trip_times_trip_event_unique');
        });
    }
};
