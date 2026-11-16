<p align="center"><img src="public/images/logo.png" width="160" alt="BrouwBoek"></p>

# BrouwBoek

BrouwBoek is de voorbeeldapplicatie uit het lesboek **Laravel Level 2**. Het is een beheerapp voor de fictieve **Brouwerij De Gulle Gans** in Bedum: de brouwer houdt er de voorraad bier, de klanten (slijterijen en drankwinkels), de bestellingen en de facturen mee bij.

Deze repository bevat de app zoals die eruitziet aan het einde van deel 2 (hoofdstuk 15): de complete app. De lesboeken zelf staan in de hoofdmap:

- `Laravel_Level_2_Deel_1.pdf`
- `Laravel_Level_2_Deel_2.pdf`

## Functies

- **Inloggen en registreren** met Laravel Fortify, plus een profielpagina
- **Dashboard** met een overzicht van de brouwerij
- **Producten**: bieren beheren, met voorraad en minimale voorraad
  - importeren vanuit Excel (`maatwebsite/excel`)
  - voorraadoverzicht exporteren naar Excel
- **Klanten**: slijterijen en drankwinkels beheren
- **Bestellingen**: nieuwe bestelling maken (de voorraad wordt automatisch bijgewerkt), status volgen (*Open*, *Ingepakt*, *Gefactureerd*) en een paklijst als PDF downloaden
- **Facturen**: factuur maken van een bestelling (21% btw, betaaltermijn 14 dagen), als PDF downloaden en per mail versturen via de queue

## Techniek

| Onderdeel | Gebruikt |
| --- | --- |
| Framework | Laravel 13 (PHP 8.3 of hoger) |
| Frontend | Livewire 4 + Flux, Tailwind CSS 4, Vite |
| Authenticatie | Laravel Fortify |
| PDF | barryvdh/laravel-dompdf |
| Excel | maatwebsite/excel |
| Database | SQLite (standaard) |
| Tests | PHPUnit |

## Installeren (met Laravel Herd)

1. Zet het project in je Herd-map, bijvoorbeeld `~/Herd/brouwboek`.
2. Open een terminal in de map en voer uit:

   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   touch database/database.sqlite
   php artisan migrate --seed
   npm install
   composer run dev
   ```

   Op Windows (PowerShell) gebruik je `copy .env.example .env` en `New-Item database/database.sqlite` in plaats van `cp` en `touch`.

3. Open <http://brouwboek.test> en log in met:
   - e-mailadres: `femke@degullegans.nl`
   - wachtwoord: `wachtwoord`

De seeder vult de database met acht bieren, vijf klanten en drie bestellingen om mee te oefenen.

## Pagina's

| URL | Pagina |
| --- | --- |
| `/dashboard` | Dashboard |
| `/producten` | Producten (incl. import en export) |
| `/klanten` | Klanten |
| `/bestellingen` | Overzicht bestellingen |
| `/bestellingen/nieuw` | Nieuwe bestelling |
| `/bestellingen/{id}` | Bestelling bekijken, paklijst downloaden |
| `/facturen` | Facturen |
| `/facturen/{id}/pdf` | Factuur als PDF |
| `/facturen/{id}/mail-voorbeeld` | Factuurmail bekijken in de browser (alleen om te testen) |
| `/profiel` | Profiel |

Alle pagina's zijn alleen bereikbaar als je bent ingelogd.

## Mail testen

Standaard worden mails alleen naar het logbestand geschreven (`MAIL_MAILER=log`). Wil je ze echt zien, start dan Mailpit (zie hoofdstuk 13) en zet in `.env`:

```ini
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

Facturen worden via de queue verstuurd (`QUEUE_CONNECTION=database`). Draait `composer run dev` niet, start de queue dan zelf met:

```bash
php artisan queue:work
```

## Voorbeeldbestanden

In de map `voorbeelden` staan twee Excel-bestanden om de import uit hoofdstuk 12 te proberen:

- `nieuwe-producten.xlsx`: een goed bestand
- `producten-met-fout.xlsx`: een bestand met een fout in rij 6

## Gegevens van de brouwerij

De naam, het adres, het KvK- en btw-nummer en de IBAN die op de factuur en de paklijst staan, pas je aan in `config/brewery.php`.

## Projectstructuur

```
app/
├── Actions/Fortify/   registreren, wachtwoord resetten, profiel bijwerken
├── Enums/             OrderStatus (open, ingepakt, gefactureerd)
├── Exports/           StockExport (voorraad naar Excel)
├── Imports/           ProductsImport (producten uit Excel)
├── Jobs/              SendInvoice (factuur mailen via de queue)
├── Livewire/Forms/    formulieren voor klanten en producten
├── Mail/              InvoiceMail
├── Models/            Product, Customer, Order, OrderLine, Invoice, User
└── Services/          OrderService, StockService, InvoiceService, PdfService
resources/views/
├── pages/             de Livewire-pagina's
└── pdf/               factuur en paklijst
```

## Tests draaien

```bash
composer test
```

## Licentie

Gebouwd op het Laravel-framework, dat open-source is onder de [MIT-licentie](https://opensource.org/licenses/MIT).
