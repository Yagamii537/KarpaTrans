<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();

            /*
             * IDENTIFICACIÓN
             */
            $table->string('work_order_number', 50)->unique();

            /*
             * CLIENTE / SUBCLIENTE
             */
            $table->foreignId('client_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('subclient_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('cargo_type_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();

            /*
             * DATOS ENTREGADOS POR EL CLIENTE
             */
            $table->string('booking_number', 100)->nullable();

            $table->string(
                'customer_order_number',
                100
            )->nullable();

            /*
             * OPERACIÓN
             */
            $table->string(
                'operation_type',
                50
            );

            $table->string(
                'service_type',
                50
            )->default('TRANSPORT');

            /*
             * PLANTA PRINCIPAL
             */
            $table->foreignId('plant_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();

            /*
             * ORIGEN
             *
             * Puede ser ubicación general o planta.
             */
            $table->string(
                'origin_type',
                20
            )->nullable();

            $table->foreignId('origin_location_id')
                ->nullable()
                ->constrained('locations')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('origin_plant_id')
                ->nullable()
                ->constrained('plants')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            /*
             * DESTINO
             */
            $table->string(
                'destination_type',
                20
            )->nullable();

            $table->foreignId('destination_location_id')
                ->nullable()
                ->constrained('locations')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('destination_plant_id')
                ->nullable()
                ->constrained('plants')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            /*
             * PLANIFICACIÓN
             */
            $table->date('requested_date');

            $table->time(
                'requested_time'
            )->nullable();

            /*
             * Turno/cita informado por cliente,
             * depósito, planta o puerto.
             */
            $table->dateTime(
                'appointment_at'
            )->nullable();

            /*
             * Una orden puede solicitar varios viajes.
             */
            $table->unsignedInteger(
                'requested_trips'
            )->default(1);

            /*
             * REQUERIMIENTO DE CONTENEDOR
             *
             * Todavía no asignamos un contenedor específico.
             * Eso ocurrirá en cada viaje.
             */
            $table->string(
                'requested_container_type',
                50
            )->nullable();

            $table->string(
                'requested_container_size',
                50
            )->nullable();

            /*
             * CARGA
             */
            $table->text(
                'cargo_description'
            )->nullable();

            $table->decimal(
                'estimated_weight_kg',
                12,
                2
            )->nullable();

            /*
             * ESTADO DE LA ORDEN
             */
            $table->string(
                'status',
                30
            )->default('PENDING');

            /*
             * DOCUMENTACIÓN / REFERENCIA
             */
            $table->string(
                'customer_reference',
                150
            )->nullable();

            $table->text('notes')->nullable();

            /*
             * AUDITORÍA BÁSICA
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
            $table->softDeletes();

            $table->index('booking_number');
            $table->index('customer_order_number');
            $table->index('requested_date');
            $table->index('status');
            $table->index([
                'client_id',
                'requested_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
