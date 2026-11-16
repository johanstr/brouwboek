<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // De brouwer zelf
        User::factory()->create([
            'name' => 'Femke de Vries',
            'email' => 'femke@degullegans.nl',
            'password' => 'wachtwoord',
        ]);

        // De bieren van Brouwerij De Gulle Gans
        $beers = [
            ['GG-BLOND', 'Gulle Gans Blond', 'Blond', 6.0, 1.45, 312, 96],
            ['GG-WIT', 'Wadden Wit', 'Witbier', 5.0, 1.40, 188, 96],
            ['GG-DUBBEL', 'Gansje Dubbel', 'Dubbel', 7.5, 1.65, 140, 72],
            ['GG-TRIPEL', 'Grutto Tripel', 'Tripel', 8.5, 1.85, 96, 72],
            ['GG-IPA', 'Hooiland IPA', 'IPA', 6.5, 1.75, 30, 48],
            ['GG-STOUT', 'Kwelder Stout', 'Stout', 7.0, 1.90, 64, 48],
            ['GG-SAISON', 'Zomerse Saison', 'Saison', 6.2, 1.70, 12, 48],
            ['GG-BOK', 'Najaarsbok', 'Bokbier', 7.2, 1.80, 220, 48],
        ];

        foreach ($beers as [$sku, $name, $style, $abv, $price, $stock, $min]) {
            Product::create([
                'sku' => $sku, 'name' => $name, 'style' => $style, 'abv' => $abv,
                'volume_cl' => 33, 'price' => $price, 'stock' => $stock, 'min_stock' => $min,
            ]);
        }

        // De klanten: slijterijen en drankwinkels in de buurt
        $shops = [
            ['Slijterij De Kurk', 'Jan Bakker', 'jan@dekurk.nl', 'Vismarkt 12', '9711 KT', 'Groningen'],
            ['Drankenhandel Hoekstra', 'Sanne Hoekstra', 'info@hoekstradranken.nl', 'Hoofdstraat 45', '9951 AB', 'Winsum'],
            ["Slijterij 't Vatje", 'Ahmed el Amrani', 'ahmed@hetvatje.nl', 'Kruisstraat 3', '9401 GH', 'Assen'],
            ['Bierwinkel Hoppe', 'Lotte Mulder', 'lotte@bierwinkelhoppe.nl', 'Oude Ebbingestraat 20', '9712 HC', 'Groningen'],
            ['Wijn & Spijs Haren', 'Peter Smit', 'peter@wijnenspijs.nl', 'Rijksstraatweg 150', '9752 BL', 'Haren'],
        ];

        foreach ($shops as [$name, $contact, $email, $address, $postcode, $city]) {
            Customer::create([
                'name' => $name, 'contact_name' => $contact, 'email' => $email,
                'phone' => '050-' . rand(1000000, 9999999),
                'address' => $address, 'postcode' => $postcode, 'city' => $city,
            ]);
        }

        // Een paar bestellingen om mee te oefenen
        $orders = [
            [1, '-6 days', OrderStatus::Packed, ['GG-BLOND' => 48, 'GG-TRIPEL' => 24]],
            [4, '-3 days', OrderStatus::Open, ['GG-IPA' => 24, 'GG-STOUT' => 12, 'GG-BOK' => 24]],
            [2, '-1 day', OrderStatus::Open, ['GG-WIT' => 36, 'GG-BLOND' => 24]],
        ];

        foreach ($orders as [$customerId, $date, $status, $lines]) {
            $order = Order::create([
                'customer_id' => $customerId,
                'order_date' => now()->modify($date),
                'status' => $status,
            ]);

            foreach ($lines as $sku => $quantity) {
                $product = Product::where('sku', $sku)->first();
                $order->lines()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);
            }
        }
    }
}
