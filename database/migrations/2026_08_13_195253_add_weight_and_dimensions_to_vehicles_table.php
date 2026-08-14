<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {

            // Pesos en kilogramos
            $table->decimal('tare_weight_kg', 10, 2)
                ->nullable()
                ->after('current_odometer');

            $table->decimal('max_weight_kg', 10, 2)
                ->nullable()
                ->after('tare_weight_kg');

            // Dimensiones en metros
            $table->decimal('length_m', 8, 2)
                ->nullable()
                ->after('max_weight_kg');

            $table->decimal('width_m', 8, 2)
                ->nullable()
                ->after('length_m');

            $table->decimal('height_m', 8, 2)
                ->nullable()
                ->after('width_m');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'tare_weight_kg',
                'max_weight_kg',
                'length_m',
                'width_m',
                'height_m',
            ]);
        });
    }
};
