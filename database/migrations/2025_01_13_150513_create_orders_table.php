<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('msisdn');
            $table->text('address');
            $table->string('state');
            $table->string('city');
            $table->enum('status', ['order', 'cancel', 'confirmed'])->default('order');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->decimal('total_quantity', 10, 2)->default(0);
            $table->text('content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
