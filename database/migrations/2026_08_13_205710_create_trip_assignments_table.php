<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_assignments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('trip_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('driver_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('vehicle_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('chassis_id')
                ->nullable()
                ->constrained('chassis')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('container_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            /*
             * Inicio y fin de esta asignación.
             * Si unassigned_at = NULL,
             * es la asignación vigente.
             */
            $table->dateTime('assigned_at');

            $table->dateTime('unassigned_at')
                ->nullable();

            $table->text('assignment_reason')
                ->nullable();

            $table->text('release_reason')
                ->nullable();

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('released_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'trip_id',
                'unassigned_at',
            ]);

            $table->index([
                'driver_id',
                'unassigned_at',
            ]);

            $table->index([
                'vehicle_id',
                'unassigned_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_assignments');
    }
};
