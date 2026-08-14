<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {

            $table->id();

            $table->foreignId('work_order_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('trip_number', 50)->unique();

            /*
             * Número secuencial dentro de la orden.
             * Ejemplo:
             * OT-001 → Viaje 1, Viaje 2, Viaje 3
             */
            $table->unsignedInteger('sequence_number');

            /*
             * SNAPSHOT HISTÓRICO
             *
             * RN-18 exige que si mañana cambian
             * nombre de cliente/subcliente/carga,
             * el viaje histórico no cambie.
             */
            $table->foreignId('client_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('client_name_snapshot');

            $table->foreignId('subclient_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('subclient_name_snapshot')
                ->nullable();

            $table->foreignId('cargo_type_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('cargo_type_name_snapshot')
                ->nullable();

            /*
             * Referencias operativas
             */
            $table->string('booking_number', 100)
                ->nullable();

            $table->string('customer_order_number', 100)
                ->nullable();

            $table->string('operation_type', 50);
            $table->string('service_type', 50);

            /*
             * ORIGEN
             */
            $table->string('origin_type', 20);

            $table->foreignId('origin_location_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete();

            $table->foreignId('origin_plant_id')
                ->nullable()
                ->constrained('plants')
                ->nullOnDelete();

            $table->string('origin_name_snapshot');

            /*
             * DESTINO
             */
            $table->string('destination_type', 20);

            $table->foreignId('destination_location_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete();

            $table->foreignId('destination_plant_id')
                ->nullable()
                ->constrained('plants')
                ->nullOnDelete();

            $table->string('destination_name_snapshot');

            /*
             * PLANIFICACIÓN
             */
            $table->dateTime('scheduled_start_at');

            $table->dateTime('scheduled_end_at')
                ->nullable();

            /*
             * Estados provisionales.
             * El documento indica que la nomenclatura
             * definitiva debe validarse con el cliente.
             */
            $table->enum('status', [
                'PENDING',
                'ASSIGNED',
                'IN_TRANSIT',
                'AT_DESTINATION',
                'COMPLETED',
                'CANCELLED',
            ])->default('PENDING');

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique([
                'work_order_id',
                'sequence_number',
            ]);

            $table->index('scheduled_start_at');
            $table->index('status');
            $table->index([
                'client_id',
                'scheduled_start_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
