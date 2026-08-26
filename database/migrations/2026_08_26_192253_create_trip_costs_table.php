<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_costs', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | VIAJE
            |--------------------------------------------------------------------------
            */

            $table->foreignId('trip_id')
                ->constrained('trips')
                ->restrictOnDelete();

            /*
             * Si el costo pertenece específicamente
             * a una transferencia del viaje.
             */
            $table->foreignId('trip_transfer_id')
                ->nullable()
                ->constrained('trip_transfers')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | TIPO DE COSTO
            |--------------------------------------------------------------------------
            |
            | BASE       = tarifa principal del viaje
            | STANDBY    = horas de espera
            | TRANSFER   = transferencia adicional
            | ADDITIONAL = cualquier otro costo
            |
            */

            $table->string(
                'cost_type',
                30
            );

            /*
            |--------------------------------------------------------------------------
            | DESCRIPCIÓN
            |--------------------------------------------------------------------------
            */

            $table->string(
                'description',
                500
            );

            /*
            |--------------------------------------------------------------------------
            | CÁLCULO
            |--------------------------------------------------------------------------
            */

            $table->decimal(
                'quantity',
                12,
                3
            )
                ->default(1);

            $table->decimal(
                'unit_price',
                14,
                2
            )
                ->default(0);

            $table->decimal(
                'subtotal',
                14,
                2
            )
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | ORIGEN DEL VALOR
            |--------------------------------------------------------------------------
            |
            | MANUAL
            | STANDBY
            | TRANSFER
            |
            */

            $table->string(
                'source_type',
                30
            )
                ->default('MANUAL');

            /*
             * ID de referencia auxiliar.
             *
             * Ejemplo:
             * ID del cálculo Stand-by.
             */
            $table->unsignedBigInteger(
                'source_id'
            )
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | ESTADO
            |--------------------------------------------------------------------------
            */

            $table->string(
                'status',
                30
            )
                ->default('PENDING');

            /*
            |--------------------------------------------------------------------------
            | OBSERVACIONES
            |--------------------------------------------------------------------------
            */

            $table->text('notes')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | AUDITORÍA
            |--------------------------------------------------------------------------
            */

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | ÍNDICES
            |--------------------------------------------------------------------------
            */

            $table->index([
                'trip_id',
                'cost_type',
            ]);

            $table->index([
                'trip_transfer_id',
                'cost_type',
            ]);

            $table->index([
                'source_type',
                'source_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'trip_costs'
        );
    }
};
