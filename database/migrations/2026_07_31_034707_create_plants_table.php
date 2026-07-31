<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('name');
            $table->string('code', 50)->nullable();

            $table->string('city')->nullable();
            $table->text('address');
            $table->string('reference')->nullable();

            $table->string('contact_name')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            /*
             * Reglas opcionales propias de la planta.
             * Si están vacías, se utilizan las del cliente.
             */
            $table->unsignedInteger('free_loading_hours')->nullable();
            $table->unsignedInteger('free_unloading_hours')->nullable();

            $table->enum('service_time_start', [
                'requested_time',
                'arrival_time',
            ])->nullable();

            $table->unsignedInteger('standby_fraction_minutes')->nullable();

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['client_id', 'is_active']);
            $table->unique(['client_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plants');
    }
};
