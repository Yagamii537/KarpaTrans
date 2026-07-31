<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('code', 50)->nullable()->unique();

            $table->enum('type', [
                'PORT',
                'DEPOT',
                'YARD',
                'WAREHOUSE',
                'EXTERNAL_PLANT',
                'WORKSHOP',
                'CUSTOMER_LOCATION',
                'OTHER',
            ]);

            $table->string('city', 150)->nullable();
            $table->string('province', 150)->nullable();
            $table->text('address');
            $table->string('reference')->nullable();

            $table->string('contact_name')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->string('opening_time', 5)->nullable();
            $table->string('closing_time', 5)->nullable();

            $table->boolean('receives_empty_containers')->default(false);
            $table->boolean('receives_full_containers')->default(false);
            $table->boolean('requires_appointment')->default(false);

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('type');
            $table->index('city');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
