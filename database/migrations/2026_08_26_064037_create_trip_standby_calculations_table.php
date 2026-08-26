<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_standby_calculations', function (Blueprint $table) {

            $table->id();

            $table
                ->foreignId('trip_id')
                ->constrained('trips')
                ->cascadeOnDelete();

            /*
             * LOAD / UNLOAD
             */
            $table
                ->string('process_type', 30)
                ->nullable();

            /*
             * REQUESTED_TIME / ARRIVAL_TIME
             */
            $table
                ->string('count_start_type', 30);

            /*
             * Evento utilizado para terminar
             * el cálculo.
             */
            $table
                ->string('end_event_type', 50)
                ->nullable();

            /*
             * Snapshot de la regla.
             */
            $table
                ->unsignedInteger('free_hours')
                ->default(0);

            $table
                ->unsignedInteger('fraction_minutes')
                ->default(30);

            $table
                ->string('rule_source', 30)
                ->nullable();

            /*
             * Fechas utilizadas.
             */
            $table
                ->dateTime('requested_at')
                ->nullable();

            $table
                ->dateTime('arrival_at')
                ->nullable();

            $table
                ->dateTime('start_at')
                ->nullable();

            $table
                ->dateTime('end_at')
                ->nullable();

            /*
             * Resultados.
             */
            $table
                ->unsignedInteger('total_minutes')
                ->nullable();

            $table
                ->unsignedInteger('free_minutes')
                ->default(0);

            $table
                ->unsignedInteger('excess_minutes')
                ->default(0);

            $table
                ->unsignedInteger('billable_hours')
                ->default(0);

            /*
             * Estado del cálculo.
             *
             * PENDING:
             * falta información.
             *
             * CALCULATED:
             * cálculo completo.
             */
            $table
                ->string('status', 30)
                ->default('PENDING');

            $table
                ->text('observation')
                ->nullable();

            $table
                ->timestamp('calculated_at')
                ->nullable();

            $table
                ->foreignId('calculated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            /*
             * Un cálculo actual por viaje.
             */
            $table->unique('trip_id');

            $table->index('status');
            $table->index('process_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'trip_standby_calculations'
        );
    }
};
