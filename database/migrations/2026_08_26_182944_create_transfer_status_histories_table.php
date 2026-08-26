<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_status_histories', function (Blueprint $table) {

            $table->id();

            $table->foreignId('trip_transfer_id')
                ->constrained('trip_transfers')
                ->cascadeOnDelete();

            $table->string('previous_status', 30)
                ->nullable();

            $table->string('new_status', 30);

            $table->text('reason')
                ->nullable();

            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('changed_at');

            $table->timestamps();

            $table->index([
                'trip_transfer_id',
                'changed_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_status_histories');
    }
};
