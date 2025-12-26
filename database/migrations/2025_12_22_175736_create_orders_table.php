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
            $table->id(); // Primary key
            $table->string('customer_name'); // Customer name
            $table->string('customer_email'); // Customer email
            $table->string('customer_phone'); // Customer phone
            $table->text('customer_address'); // Customer address
            $table->decimal('total_amount', 10, 2); // Total order amount
            $table->string('status')->default('pending'); // Order status
            $table->timestamps(); // created_at & updated_at
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
