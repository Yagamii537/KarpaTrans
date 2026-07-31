<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();

            // Información personal
            $table->string('first_names');
            $table->string('last_names');
            $table->string('identification', 20)->unique();
            $table->date('birth_date')->nullable();

            // Contacto
            $table->string('phone', 30)->nullable();
            $table->string('secondary_phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();

            // Licencia
            $table->string('license_number', 50)->unique();
            $table->string('license_type', 20);
            $table->date('license_issue_date')->nullable();
            $table->date('license_expiration_date');
            $table->unsignedInteger('license_points')->nullable();

            // Contacto de emergencia
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();
            $table->string('emergency_contact_relationship', 100)->nullable();

            // Archivos
            $table->string('photo')->nullable();
            $table->string('identification_document')->nullable();
            $table->string('license_document')->nullable();

            // Información laboral
            $table->date('hire_date')->nullable();
            $table->string('employee_code', 50)->nullable()->unique();

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index('last_names');
            $table->index('license_expiration_date');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
