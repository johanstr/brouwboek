<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained();
            $table->string('number')->unique();       // bijv. 2026-0001
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('subtotal', 10, 2);       // totaal excl. btw
            $table->decimal('vat', 10, 2);            // btw-bedrag
            $table->decimal('total', 10, 2);          // totaal incl. btw
            $table->timestamp('sent_at')->nullable(); // wanneer gemaild
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
