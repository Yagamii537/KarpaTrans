<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // Información fiscal
            $table->string('business_name');
            $table->string('trade_name')->nullable();
            $table->string('identification_type', 20)->default('RUC');
            $table->string('identification', 20)->unique();

            // Contacto
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('secondary_phone', 30)->nullable();
            $table->text('address')->nullable();

            // Reglas logísticas
            $table->unsignedInteger('free_loading_hours')->default(10);
            $table->unsignedInteger('free_unloading_hours')->default(10);

            $table->enum('service_time_start', [
                'requested_time',
                'arrival_time',
            ])->default('requested_time');

            $table->unsignedInteger('standby_fraction_minutes')->default(30);

            // Configuración general
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index('business_name');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
