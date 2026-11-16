# BrouwBoek

Het project uit het lesboek **Laravel Level 2**, zoals het eruitziet aan het einde van deel 2 (hoofdstuk 15): de complete app.

## Installeren (met Laravel Herd)

1. Pak deze map uit in je Herd-map, bijvoorbeeld `~/Herd/brouwboek`.
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

3. Open http://brouwboek.test en log in met:
   - e-mailadres: `femke@degullegans.nl`
   - wachtwoord: `wachtwoord`

## Mail testen

Start Mailpit (zie hoofdstuk 13) en zet in `.env`:

```ini
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

## Voorbeeldbestanden

In de map `voorbeelden` staan twee Excel-bestanden om de import uit hoofdstuk 12 te proberen:
`nieuwe-producten.xlsx` (goed) en `producten-met-fout.xlsx` (met een fout in rij 6).
