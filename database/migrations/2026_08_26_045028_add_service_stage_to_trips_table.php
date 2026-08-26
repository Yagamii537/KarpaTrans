<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {

            /*
             * Número del servicio solicitado
             * dentro de la Orden de Trabajo.
             *
             * Ej:
             * OT solicita 3 servicios:
             * 1, 2, 3
             */
            $table->unsignedInteger('service_number')
                ->nullable()
                ->after('sequence_number');

            /*
             * Etapa física que representa
             * este viaje.
             *
             * IMMEDIATE
             * POSITIONING
             * PICKUP
             * TRANSFER
             */
            $table->string('service_stage', 40)
                ->nullable()
                ->after('service_number');

            $table->index([
                'work_order_id',
                'service_number',
            ]);

            $table->index([
                'work_order_id',
                'service_stage',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {

            $table->dropIndex([
                'work_order_id',
                'service_number',
            ]);

            $table->dropIndex([
                'work_order_id',
                'service_stage',
            ]);

            $table->dropColumn([
                'service_number',
                'service_stage',
            ]);
        });
    }
};
