<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('unit', 16)->nullable();      // л, кг, шт
            $table->decimal('default_price', 10, 2)->nullable();
            $table->text('comment')->nullable();          // "3 литра", "2 кг"
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
