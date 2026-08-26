<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | EMPRESA
            |--------------------------------------------------------------------------
            */

            $table->string('company_name')
                ->nullable();

            $table->string('trade_name')
                ->nullable();

            $table->string('ruc', 20)
                ->nullable();

            $table->string('phone', 50)
                ->nullable();

            $table->string('email')
                ->nullable();

            $table->text('address')
                ->nullable();

            $table->string('logo_path')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | CONFIGURACIÓN GENERAL
            |--------------------------------------------------------------------------
            */

            $table->string('currency', 10)
                ->default('USD');

            $table->string('timezone')
                ->default('America/Guayaquil');

            /*
            |--------------------------------------------------------------------------
            | ALERTAS
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('document_alert_days')
                ->default(30);

            $table->unsignedInteger('license_alert_days')
                ->default(30);

            /*
            |--------------------------------------------------------------------------
            | NUMERACIONES
            |--------------------------------------------------------------------------
            */

            $table->string('work_order_prefix', 20)
                ->default('OT');

            $table->string('trip_prefix', 20)
                ->default('VIA');

            $table->string('transfer_prefix', 20)
                ->default('TRA');

            $table->string('settlement_prefix', 20)
                ->default('LIQ');

            /*
            |--------------------------------------------------------------------------
            | PARÁMETROS ECONÓMICOS
            |--------------------------------------------------------------------------
            */

            $table->decimal(
                'vat_percentage',
                5,
                2
            )
                ->default(15);

            $table->unsignedInteger('decimal_places')
                ->default(2);

            /*
            |--------------------------------------------------------------------------
            | AUDITORÍA
            |--------------------------------------------------------------------------
            */

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'system_settings'
        );
    }
};
