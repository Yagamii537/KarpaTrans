<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chassis', function (Blueprint $table) {
            $table->id();

            /*
             * Vehículo al que está asignado habitualmente.
             * Puede quedar vacío porque el chasis puede utilizarse
             * con diferentes cabezales.
             */
            $table->foreignId('vehicle_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->string('code', 50)->unique();
            $table->string('plate', 20)->nullable()->unique();

            $table->string('chassis_type', 50);
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('color', 50)->nullable();

            $table->string('serial_number', 100)->nullable()->unique();

            $table->unsignedInteger('axles')->nullable();
            $table->decimal('maximum_capacity_tons', 10, 2)->nullable();

            $table->boolean('supports_20ft')->default(true);
            $table->boolean('supports_40ft')->default(true);
            $table->boolean('supports_reefer')->default(false);

            $table->date('registration_expiration_date')->nullable();
            $table->date('technical_review_expiration_date')->nullable();

            $table->string('photo')->nullable();
            $table->string('registration_document')->nullable();
            $table->string('technical_review_document')->nullable();

            $table->enum('operational_status', [
                'AVAILABLE',
                'ASSIGNED',
                'MAINTENANCE',
                'OUT_OF_SERVICE',
            ])->default('AVAILABLE');

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index('operational_status');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chassis');
    }
};
