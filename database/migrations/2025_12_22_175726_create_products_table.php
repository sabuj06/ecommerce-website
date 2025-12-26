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
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // Foreign key to categories
            $table->string('name'); // Product name
            $table->string('slug')->unique(); // Unique slug
            $table->text('description'); // Product description
            $table->decimal('price', 10, 2); // Price
            $table->integer('stock'); // Stock quantity
            $table->string('image')->nullable(); // Optional image
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
