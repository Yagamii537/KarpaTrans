<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_events', function (Blueprint $table) {

            $table->id();

            $table->foreignId('trip_transfer_id')
                ->constrained('trip_transfers')
                ->cascadeOnDelete();

            $table->enum('event_type', [
                'ARRIVAL_ORIGIN',
                'DEPARTURE_ORIGIN',
                'ARRIVAL_DESTINATION',
                'DELIVERY',
            ]);

            $table->dateTime('event_at');

            $table->enum('location_type', [
                'LOCATION',
                'PLANT',
                'NONE',
            ])->default('NONE');

            $table->foreignId('location_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete();

            $table->foreignId('plant_id')
                ->nullable()
                ->constrained('plants')
                ->nullOnDelete();

            $table->string('location_name_snapshot')
                ->nullable();

            $table->text('observation')
                ->nullable();

            $table->boolean('is_manual')
                ->default(true);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            /*
             * Un evento operativo de cada tipo
             * por transferencia.
             */
            $table->unique([
                'trip_transfer_id',
                'event_type',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_events');
    }
};
