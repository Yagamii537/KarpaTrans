<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('container_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('container_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('from_location_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('to_location_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->enum('movement_type', [
                'INITIAL',
                'PICKUP',
                'DELIVERY',
                'TRANSFER',
                'RETURN',
                'POSITIONING',
                'OTHER',
            ]);

            $table->dateTime('movement_at');

            $table->string('reference_type', 100)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->string('seal_number', 50)->nullable();

            $table->enum('load_status', [
                'EMPTY',
                'FULL',
                'UNKNOWN',
            ])->default('UNKNOWN');

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'container_id',
                'movement_at',
            ]);

            $table->index([
                'reference_type',
                'reference_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('container_movements');
    }
};
