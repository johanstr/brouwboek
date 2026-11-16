<?php

use App\Http\Controllers\PdfController;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// Alleen voor ingelogde gebruikers
Route::middleware('auth')->group(function () {
    Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard');

    Route::livewire('/producten', 'pages::products.index')->name('products.index');
    Route::livewire('/klanten', 'pages::customers.index')->name('customers.index');

    Route::livewire('/bestellingen', 'pages::orders.index')->name('orders.index');
    Route::livewire('/bestellingen/nieuw', 'pages::orders.create')->name('orders.create');
    Route::livewire('/bestellingen/{order}', 'pages::orders.show')->name('orders.show');
    Route::get('/bestellingen/{order}/paklijst', [PdfController::class, 'packingList'])->name('orders.packing-list');

    Route::livewire('/facturen', 'pages::invoices.index')->name('invoices.index');
    Route::get('/facturen/{invoice}/pdf', [PdfController::class, 'invoice'])->name('invoices.pdf');

    // Alleen om te testen: bekijk de factuurmail in je browser
    Route::get('/facturen/{invoice}/mail-voorbeeld', fn (Invoice $invoice) => new InvoiceMail($invoice))
        ->name('invoices.mail-preview');

    Route::livewire('/profiel', 'pages::profile')->name('profile');
});
