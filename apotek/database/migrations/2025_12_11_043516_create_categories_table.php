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
    Schema::create('categories', function (Blueprint $table) {
        $table->bigIncrements('id');               // bigint(20) unsigned AUTO_INCREMENT
        $table->string('name', 255);               // varchar(255)
        $table->string('slug', 255)->index();      // varchar(255) + index
        $table->text('description')->nullable();   // text NULL
        $table->timestamp('created_at')->nullable();
        $table->timestamp('updated_at')->nullable();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
