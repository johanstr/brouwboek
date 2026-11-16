<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->unsignedInteger('quantity');
            $table->decimal('price', 8, 2);           // prijs per fles op het moment van bestellen
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_lines');
    }
};
