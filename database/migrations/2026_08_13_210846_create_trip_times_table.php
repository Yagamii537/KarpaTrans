<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_times', function (Blueprint $table) {

            $table->id();

            $table->foreignId('trip_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            /*
             * Tipos iniciales de eventos.
             * Pueden ampliarse después según la operación real.
             */
            $table->enum('event_type', [
                'ARRIVAL',
                'ENTRY',
                'CONTAINER_PICKUP',
                'LOAD_START',
                'LOAD_END',
                'UNLOAD_START',
                'UNLOAD_END',
                'WAIT_START',
                'WAIT_END',
                'DEPARTURE',
                'POSITIONING',
                'PICKUP',
                'PORT_ARRIVAL',
                'DELIVERY',
                'OTHER',
            ]);

            $table->dateTime('event_at');

            /*
             * La ubicación puede ser:
             * - una ubicación general
             * - una planta
             */
            $table->enum('location_type', [
                'LOCATION',
                'PLANT',
                'NONE',
            ])->default('NONE');

            $table->foreignId('location_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('plant_id')
                ->nullable()
                ->constrained('plants')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            /*
             * Snapshot para que el historial
             * no cambie si el catálogo cambia.
             */
            $table->string(
                'location_name_snapshot'
            )->nullable();

            $table->text('observation')
                ->nullable();

            /*
             * Indica si el registro fue
             * ingresado manualmente.
             */
            $table->boolean('is_manual')
                ->default(true);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'trip_id',
                'event_at',
            ]);

            $table->index([
                'trip_id',
                'event_type',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_times');
    }
};
