<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {

            /*
             * Peso bruto máximo permitido
             * del vehículo.
             */
            $table->decimal(
                'gross_weight_kg',
                12,
                2
            )
                ->nullable()
                ->after('tare_weight_kg');


            /*
             * Capacidad real de carga útil.
             *
             * Este es el campo que después
             * usaremos para validar la OT.
             */
            $table->decimal(
                'max_load_capacity_kg',
                12,
                2
            )
                ->nullable()
                ->after('gross_weight_kg');


            /*
             * Número de ejes.
             */
            $table->unsignedTinyInteger(
                'axles'
            )
                ->nullable()
                ->after('height_m');


            /*
             * Volumen útil aproximado.
             */
            $table->decimal(
                'volume_m3',
                10,
                2
            )
                ->nullable()
                ->after('axles');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {

            $table->dropColumn([
                'gross_weight_kg',
                'max_load_capacity_kg',
                'axles',
                'volume_m3',
            ]);
        });
    }
};
