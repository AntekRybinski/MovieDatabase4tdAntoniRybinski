# Sesja — Frontend Agent

## Zadanie

Złożyć trzy ekrany, ciemny motyw filmowy i sprawdzenie formularza recenzji w przeglądarce. Nie dublować zapytań SQL, które trzyma Backend Agent.

## Co zostało zrobione

`index.php`

- siatka kart: gatunek, odznaka średniej, rok, liczba recenzji, skrócony opis, odnośnik `film.php?id=X`,
- formularz GET z polem tytułu i listą gatunków pobraną z bazy,
- pusty wynik wyszukiwania ma własny komunikat,
- sekcja `#dodaj` z trzema formularzami: film, aktor, rola. Po błędzie walidacji otwiera się ten panel, którego dotyczył zapis, a pola zachowują wpisane wartości.

`film.php`

- tytuł, rok, gatunek, średnia,
- opis i lista obsady; każda osoba jest odnośnikiem `aktor.php?id=Y` z nazwą roli,
- lista recenzji z datą i odznaką `n/10`,
- formularz autora, oceny i treści (`id="form-recenzja"`, `novalidate`).

`aktor.php`

- imię i nazwisko, notka,
- lista filmów z rokiem, gatunkiem, rolą, średnią i odnośnikiem do filmu.

`frontend/css/style.css`

- ciemne tło, złoty akcent, karty na siatce `auto-fill`,
- odznaki: wysoka, średnia, niska i „Brak ocen”,
- formularze, alerty sukcesu i błędu, przyklejony pasek nawigacji,
- poniżej 800 px katalog, wyszukiwarka i układ filmu schodzą do jednej kolumny.

`frontend/js/main.js`

- po załadowaniu strony szuka `#form-recenzja` i nic nie robi na katalogu oraz profilu aktora,
- przed wysłaniem sprawdza, czy autor, ocena i treść nie są puste,
- ocenę przyjmuje tylko jako ciąg cyfr z zakresu 1–10 (odrzuca `7.5`, `0`, `11` i tekst),
- błąd wstawia do `#blad-formularza` i anuluje wysłanie.
- Przycisk zostaje aktywny po poprawnej kontroli. Wyłączenie go w trakcie zdarzenia `submit` potrafiło przerwać wysyłkę w przeglądarce, zanim PHP zobaczyło żądanie. Przed podwójnym wpisem chroni przekierowanie PRG.

Tekst z bazy i z formularza jest wstawiany przez `h()`, także w atrybutach `value`. Opisy i recenzje dostają `nl2br` dopiero po zakodowaniu HTML.

## Problemy

Samo `min` i `max` na polu liczbowym nie jest walidacją, na której można oprzeć wymaganie INF.03. Przeglądarka potrafi je pominąć, a część testów QA szła surowym POST. Formularz ma więc `novalidate` i własny komunikat, a ostateczną decyzję i tak wydaje PHP. Frontend nie próbuje „naprawiać” skali po cichu.

Wspólne menu musiało działać z trzech plików bez dodatkowego `header.php`. Użyto funkcji `naglowek()` i `stopka()` przygotowanych przez Backend Agenta. Ścieżki do CSS i JS są względne (`frontend/css/style.css`), bo wszystkie strony leżą w korzeniu projektu.

Kontrast odznak na ciemnym tle był za słaby przy pierwszej próbie złotego tekstu na bursztynowym tle. Odznaki dostały osobne, ciemniejsze tła i jasny tekst dla czterech stanów oceny.

## Wynik sesji

Trzy ekrany prowadzą jeden w drugi tymi adresami, których wymaga polecenie. Średnia jest odznaką, brak ocen ma osobny wygląd, a formularz recenzji nie wysyła pustych pól ani oceny spoza 1–10, dopóki działa JavaScript.
