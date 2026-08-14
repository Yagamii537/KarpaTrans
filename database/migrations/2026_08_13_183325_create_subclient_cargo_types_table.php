<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subclient_cargo_types', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subclient_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('cargo_type_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->timestamps();

            $table->unique([
                'subclient_id',
                'cargo_type_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subclient_cargo_types');
    }
};
