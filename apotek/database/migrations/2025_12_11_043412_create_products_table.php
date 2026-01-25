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
        $table->bigIncrements('id'); // bigint unsigned AUTO_INCREMENT
        $table->string('name', 255);
        $table->string('slug', 255)->index();
        $table->text('description')->nullable();
        
        // Foreign key to categories
        $table->unsignedBigInteger('category_id');
        $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

        // Basic product fields
        $table->decimal('price', 10, 2)->default(0);
        $table->integer('stock')->default(0);
        $table->string('image')->nullable();

        $table->timestamp('created_at')->nullable();
        $table->timestamp('updated_at')->nullable();
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
