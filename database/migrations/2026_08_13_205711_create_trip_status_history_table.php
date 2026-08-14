<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_status_history', function (Blueprint $table) {

            $table->id();

            $table->foreignId('trip_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('previous_status', 30)
                ->nullable();

            $table->string('new_status', 30);

            $table->text('reason')->nullable();

            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('changed_at');

            $table->timestamps();

            $table->index([
                'trip_id',
                'changed_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'trip_status_history'
        );
    }
};
