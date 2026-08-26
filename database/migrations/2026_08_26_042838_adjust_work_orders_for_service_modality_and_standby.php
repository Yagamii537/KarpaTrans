<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {

            /*
             * MODALIDAD OPERATIVA
             *
             * IMMEDIATE
             * POSITIONING
             * PICKUP
             * POSITIONING_PICKUP
             */
            $table->string('service_modality', 40)
                ->nullable()
                ->after('service_type');

            /*
             * SNAPSHOT DE REGLA STAND-BY
             *
             * Estos valores quedan congelados en la OT.
             * Si mañana cambia la configuración del cliente,
             * la OT histórica mantiene su regla original.
             */
            $table->string('standby_process_type', 30)
                ->nullable()
                ->after('requested_trips');

            $table->unsignedInteger('standby_free_hours')
                ->nullable()
                ->after('standby_process_type');

            $table->string('standby_count_start_type', 30)
                ->nullable()
                ->after('standby_free_hours');

            $table->unsignedInteger('standby_fraction_minutes')
                ->nullable()
                ->after('standby_count_start_type');

            /*
             * CLIENT / SUBCLIENT / OVERRIDE
             */
            $table->string('standby_rule_source', 30)
                ->nullable()
                ->after('standby_fraction_minutes');

            /*
             * EXCEPCIÓN MANUAL
             */
            $table->boolean('standby_rule_overridden')
                ->default(false)
                ->after('standby_rule_source');

            $table->text('standby_override_reason')
                ->nullable()
                ->after('standby_rule_overridden');

            $table->foreignId('standby_override_by')
                ->nullable()
                ->after('standby_override_reason')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {

            $table->dropForeign([
                'standby_override_by',
            ]);

            $table->dropColumn([
                'service_modality',

                'standby_process_type',
                'standby_free_hours',
                'standby_count_start_type',
                'standby_fraction_minutes',

                'standby_rule_source',
                'standby_rule_overridden',
                'standby_override_reason',
                'standby_override_by',
            ]);
        });
    }
};
