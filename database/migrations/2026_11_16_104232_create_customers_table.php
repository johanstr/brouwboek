<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');                   // bijv. Slijterij De Kurk
            $table->string('contact_name')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('address');
            $table->string('postcode', 7);
            $table->string('city');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
