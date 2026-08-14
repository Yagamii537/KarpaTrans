<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subclients', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('business_name');
            $table->string('trade_name')->nullable();

            $table->string('identification_type', 20)->nullable();
            $table->string('identification', 20)->nullable();

            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();

            $table->text('address')->nullable();
            $table->text('notes')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['client_id', 'is_active']);
            $table->index('business_name');

            $table->unique([
                'client_id',
                'business_name'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subclients');
    }
};
