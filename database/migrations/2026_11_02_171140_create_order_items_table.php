<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete(); // требование №4
            $table->decimal('quantity', 10, 3)->default(1);
            $table->string('unit', 16)->nullable();
            $table->decimal('price', 10, 2)->nullable();     // фактическая цена (требование №7)
            $table->boolean('is_purchased')->default(false);
            $table->timestamp('purchased_at')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'is_purchased']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
