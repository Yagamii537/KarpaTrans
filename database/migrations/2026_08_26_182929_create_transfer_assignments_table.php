<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_assignments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('trip_transfer_id')
                ->constrained('trip_transfers')
                ->cascadeOnDelete();

            $table->foreignId('driver_id')
                ->constrained('drivers')
                ->restrictOnDelete();

            $table->foreignId('vehicle_id')
                ->constrained('vehicles')
                ->restrictOnDelete();

            $table->foreignId('chassis_id')
                ->nullable()
                ->constrained('chassis')
                ->nullOnDelete();

            $table->foreignId('container_id')
                ->nullable()
                ->constrained('containers')
                ->nullOnDelete();

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
                'trip_transfer_id',
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
        Schema::dropIfExists('transfer_assignments');
    }
};
