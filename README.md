# projektIP2
Projekt na IP - Prohlížeč Databáze, verze 2 Max Extract Pressure-Pro, model sixty
Jonáš Karpiš
3.A 2023
DELTA, SŠIE s.r.o.

Projekt zobrazuje databázi místností a zaměstnanců

Pro spuštění je třeba:

Vytvořit ve složce /config soubor local-config.js
Obsah souboru by měl vypadat:

{
    "db": {
    "user" : "vas_user",
    "password" : "vase_heslo"
    }
}

Dále doplnit v souboru /config/config.js prázdné políčka.

Potřebné balíčky se dají doinstalovat příkazem 'composer update' v rootu.

Využití technologie:
PHP 8.1
Tracy 2.5
hassankhan 3.0
mustache 2.14
