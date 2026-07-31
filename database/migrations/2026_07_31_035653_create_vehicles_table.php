<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            $table->string('plate', 15)->unique();
            $table->string('internal_code', 50)->nullable()->unique();

            $table->string('brand', 100);
            $table->string('model', 100);
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('color', 50)->nullable();

            $table->string('vehicle_type', 50)->default('TRACTOCAMION');

            $table->string('chassis_number', 100)->nullable()->unique();
            $table->string('engine_number', 100)->nullable()->unique();

            $table->string('ownership_type', 30)->default('PROPIO');
            $table->string('owner_name')->nullable();
            $table->string('owner_identification', 20)->nullable();

            $table->decimal('fuel_capacity', 10, 2)->nullable();
            $table->decimal('current_odometer', 12, 2)->nullable();

            $table->date('registration_expiration_date')->nullable();
            $table->date('technical_review_expiration_date')->nullable();
            $table->date('insurance_expiration_date')->nullable();

            $table->string('photo')->nullable();
            $table->string('registration_document')->nullable();
            $table->string('insurance_document')->nullable();
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

            $table->index('plate');
            $table->index('operational_status');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
