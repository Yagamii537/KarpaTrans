<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('containers', function (Blueprint $table) {
            $table->id();

            $table->string('container_number', 30)->unique();

            $table->enum('container_type', [
                'DRY',
                'REEFER',
                'OPEN_TOP',
                'FLAT_RACK',
                'TANK',
                'OTHER',
            ])->default('DRY');

            $table->enum('container_size', [
                '20FT',
                '40FT',
                '40HC',
                '45FT',
                'OTHER',
            ])->default('40FT');

            $table->enum('load_status', [
                'EMPTY',
                'FULL',
                'UNKNOWN',
            ])->default('UNKNOWN');

            $table->enum('operational_status', [
                'AVAILABLE',
                'ASSIGNED',
                'IN_TRANSIT',
                'AT_CLIENT',
                'AT_PORT',
                'AT_DEPOT',
                'MAINTENANCE',
                'OUT_OF_SERVICE',
            ])->default('AVAILABLE');

            $table->foreignId('current_location_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->string('seal_number', 50)->nullable();

            $table->decimal('tare_weight_kg', 10, 2)->nullable();
            $table->decimal('max_gross_weight_kg', 10, 2)->nullable();

            $table->string('shipping_line')->nullable();

            $table->date('last_inspection_date')->nullable();

            $table->text('notes')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index('container_number');
            $table->index('container_type');
            $table->index('container_size');
            $table->index('operational_status');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};
