<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_transfers', function (Blueprint $table) {

            $table->id();

            /*
             * Viaje principal donde apareció
             * la necesidad de transferencia.
             */
            $table->foreignId('trip_id')
                ->constrained('trips')
                ->cascadeOnUpdate()
                ->restrictOnDelete();


            /*
             * Número propio de transferencia.
             *
             * Ej:
             * TRA-2026-000001
             */
            $table->string(
                'transfer_number',
                50
            )->unique();


            /*
            |--------------------------------------------------------------------------
            | ORIGEN
            |--------------------------------------------------------------------------
            */

            $table->enum(
                'origin_type',
                [
                    'LOCATION',
                    'PLANT',
                ]
            );

            $table->foreignId(
                'origin_location_id'
            )
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete();

            $table->foreignId(
                'origin_plant_id'
            )
                ->nullable()
                ->constrained('plants')
                ->nullOnDelete();

            /*
             * Snapshot para conservar
             * el nombre histórico.
             */
            $table->string(
                'origin_name_snapshot'
            );


            /*
            |--------------------------------------------------------------------------
            | DESTINO
            |--------------------------------------------------------------------------
            */

            $table->enum(
                'destination_type',
                [
                    'LOCATION',
                    'PLANT',
                ]
            );

            $table->foreignId(
                'destination_location_id'
            )
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete();

            $table->foreignId(
                'destination_plant_id'
            )
                ->nullable()
                ->constrained('plants')
                ->nullOnDelete();

            $table->string(
                'destination_name_snapshot'
            );


            /*
            |--------------------------------------------------------------------------
            | FECHAS
            |--------------------------------------------------------------------------
            */

            $table->dateTime(
                'scheduled_at'
            )->nullable();

            $table->dateTime(
                'started_at'
            )->nullable();

            $table->dateTime(
                'completed_at'
            )->nullable();


            /*
            |--------------------------------------------------------------------------
            | ESTADO
            |--------------------------------------------------------------------------
            */

            $table->enum(
                'status',
                [
                    'PENDING',
                    'ASSIGNED',
                    'IN_TRANSIT',
                    'COMPLETED',
                    'CANCELLED',
                ]
            )->default('PENDING');


            /*
            |--------------------------------------------------------------------------
            | INFORMACIÓN
            |--------------------------------------------------------------------------
            */

            $table->text(
                'reason'
            );

            $table->text(
                'notes'
            )->nullable();


            /*
            |--------------------------------------------------------------------------
            | AUDITORÍA
            |--------------------------------------------------------------------------
            */

            $table->foreignId(
                'created_by'
            )
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId(
                'updated_by'
            )
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();


            $table->timestamps();

            $table->softDeletes();


            /*
            |--------------------------------------------------------------------------
            | ÍNDICES
            |--------------------------------------------------------------------------
            */

            $table->index([
                'trip_id',
                'status',
            ]);

            $table->index(
                'scheduled_at'
            );
        });
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'trip_transfers'
        );
    }
};
