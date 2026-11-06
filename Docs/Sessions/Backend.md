# Sesja — Backend Agent

## Zadanie

Połączyć PHP z bazą `baza_filmow` i wystawić funkcje, z których korzystają trzy strony. Zabezpieczyć wejście i wyjście. Po uwadze QA domknąć skalę oceny i wzorzec PRG.

## Co zostało zrobione

`backend/config/db.php` otwiera `mysqli` na `localhost`, użytkowniku `root`, pustym haśle i bazie `baza_filmow`, potem wywołuje `set_charset('utf8mb4')`. Błąd połączenia kończy się krótkim komunikatem po polsku, bez wypisywania hasła. Sterownik działa w trybie wyjątków, a nieobsłużony wyjątek nie pokazuje zapytania SQL użytkownikowi.

`backend/includes/functions.php` zawiera:

| Funkcja | Rola |
| --- | --- |
| `pobierzFilmy` | Katalog z filtrem tytułu (`LIKE`) i gatunku oraz `ROUND(AVG(ocena), 1)` i `COUNT` |
| `pobierzFilm` | Jeden film albo `null`, identyfikator odrzucany gdy nie jest dodatnią liczbą |
| `pobierzObsade` | Aktorzy filmu, `INNER JOIN` |
| `pobierzRecenzje` | Opinie od najnowszej |
| `pobierzAktora` | Profil |
| `pobierzFilmyAktora` | Filmografia z nazwą roli i średnią |
| `zapiszRecenzje` | Walidacja i `INSERT` |
| `zapiszFilm`, `zapiszAktora`, `zapiszRole` | Dopisywanie katalogu i obsady |
| `formatujOcene` | Liczba z przecinkiem albo tekst „Brak ocen” |
| `h` | `htmlspecialchars` przy wypisywaniu |
| `przekieruj` | `header('Location')` i `exit` |
| `naglowek`, `stopka` | Wspólna ramka HTML |

Identyfikatory z `$_GET` przechodzą przez `FILTER_VALIDATE_INT`. Tekst z formularzy jest obcinany do długości kolumn. Wzorzec `LIKE` ma zneutralizowane znaki `%` i `_`, żeby wyszukiwarka nie zamieniła ich w maskę. Zapytania z danymi użytkownika są przygotowane (`prepare`, `bind_param`).

`film.php` woła `zapiszRecenzje()`. Przy wyniku `true` wykonuje:

`Location: film.php?id={id}&status=recenzja`

`index.php` robi to samo dla filmu, aktora i roli (`status=film|aktor|obsada`). Komunikat sukcesu jest białą listą tych trzech wartości, a nie surowym tekstem z adresu.

## Problemy

**Iteracja 1, usterka od QA.** Pierwsze złożenie ufało atrybutom `min` i `max` pola liczbowego oraz skryptowi w przeglądarce. Żądanie POST złożone poza formularzem, z oceną `0` albo `11`, przechodziło do `INSERT`. Druga usterka: po udanym POST strona filmu rysowała się od razu. F5 powtarzało POST i powstawał drugi, taki sam wpis.

**Poprawka.** Doszła funkcja `walidujOcene()` oparta o `FILTER_VALIDATE_INT` z `min_range = 1` i `max_range = 10`. Wartość spoza zakresu, liczba zmiennoprzecinkowa i tekst zwracają komunikat „Ocena musi być liczbą całkowitą od 1 do 10.” i nie dotykają bazy. Puste pola autora i treści są odrzucane tak samo. Udany zapis nie renderuje HTML. Wysyłany jest nagłówek `Location`, potem `exit`, zanim powstanie jakikolwiek znacznik. Odświeżenie trafia w GET.

Osobnym problemem technicznym było wiązanie parametrów `mysqli` na różnych wersjach PHP z XAMPP. Argumenty `bind_param` są składane jako referencje i rozpakowywane w `powiazParametry()`, żeby prepared statements działały stabilnie.

Wspólny nagłówek nie dostał własnego pliku. Lista plików z polecenia była zamknięta, więc ramka strony jest funkcją w `functions.php`.

## Wynik sesji

Odczyt katalogu, szczegółów, obsady i profilu aktora idzie przez jedną warstwę. Zapis recenzji ma serwerową skalę 1–10 i PRG. QA w iteracji 2 nie zgłosił już tych dwóch usterek.
