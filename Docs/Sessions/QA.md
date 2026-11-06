# Sesja — QA Agent

## Zadanie

Sprawdzić aplikację względem kryteriów z `Docs/Specs.md`. Nie kończyć na pierwszym przebiegu: usterki wracają do autora warstwy, a potem test jest powtarzany.

## Iteracja 1

Przejście po katalogu, wyszukiwarce, szczegółach, obsadzie i profilu aktora nie wykazało rozjazdów ze schematem. Prepared statements i `htmlspecialchars` były na miejscu. Dwie kontrole zapisu recenzji zakończyły się FAIL i wróciły do Backend Agenta.

1. **Skala oceny tylko w przeglądarce.** Pole miało `min` i `max`, a skrypt JS odrzucał wartość spoza 1–10. Żądanie POST wysłane z pominięciem tego skryptu, z oceną `0` oraz z oceną `11`, nie było zatrzymywane w PHP, zanim polecenie `INSERT` dotarło do bazy. Ograniczenie `CHECK` w SQL nie zastępuje komunikatu walidacji w aplikacji i na części konfiguracji bywa i tak pomijane.
2. **Duplikat po F5.** Udany POST rysował stronę filmu od razu. Odświeżenie powtarzało ostatnie żądanie POST, więc ta sama recenzja pojawiała się drugi raz. Brakowało przekierowania `Location` po zapisie.

Obie usterki opisano w `Docs/QA-Report.md` i przekazano Backend Agentowi. Frontend nie był adresatem: JS działał, a dziura była po stronie serwera.

## Poprawka, którą sprawdzono ponownie

Backend Agent dodał `walidujOcene()` (liczba całkowita, zakres 1–10) wewnątrz `zapiszRecenzje()` oraz przekierowanie PRG w `film.php` i w formularzach na `index.php`. Dopiero ta wersja weszła do iteracji 2.

## Iteracja 2

Ten sam zestaw przypadków, tym razem na kodzie po poprawce. Oceny `0`, `11`, `7.5` i pusty autor nie tworzą wiersza. Po udanym zapisie przeglądarka ląduje na adresie GET `film.php?id=...&status=recenzja`. Kolejne odświeżenie nie wysyła POST. Katalog, filtry, `INNER JOIN` obsady, profil aktora, „Brak ocen” dla filmu bez recenzji, dopisanie filmu i aktora oraz kodowanie HTML w treści recenzji dostały status PASS.

## Problemy organizacyjne

Część przypadków da się zobaczyć tylko surowym żądaniem, bo przeglądarka z włączonym JS w ogóle nie puści oceny `11`. Raport rozdziela więc kontrolę JS i kontrolę PHP. Obie muszą przejść. Test „Brak ocen” nie dotyczy filmów z seeda (każdy ma 2–3 recenzje). Dotyczy filmu dodanego formularzem, zanim ktokolwiek go oceni.

## Wynik sesji

Iteracja 1: dwa FAIL, odesłane do Backend Agenta. Iteracja 2: wszystkie zaplanowane przypadki PASS. Zestaw jest zgodny z listą 9/9 w sprawozdaniu końcowym.
