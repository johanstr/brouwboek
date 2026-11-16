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
            $table->string('sku')->unique();          // artikelnummer, bijv. GG-BLOND
            $table->string('name');                   // naam van het bier
            $table->string('style')->nullable();      // bierstijl, bijv. Tripel
            $table->decimal('abv', 3, 1);             // alcoholpercentage, bijv. 8.5
            $table->unsignedSmallInteger('volume_cl')->default(33); // inhoud fles in cl
            $table->decimal('price', 8, 2);           // prijs per fles, excl. btw
            $table->integer('stock')->default(0);     // aantal flessen op voorraad
            $table->integer('min_stock')->default(24);// onder dit aantal: bijbrouwen!
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
