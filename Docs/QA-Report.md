# Raport QA — Baza Filmów

Środowisko: XAMPP, Apache, MySQL/MariaDB, PHP, baza `baza_filmow` zaimportowana z `database/schema.sql`.

Zakres: katalog, wyszukiwarka, szczegóły filmu, obsada, profil aktora, zapis recenzji, dopisywanie filmu i aktora, odporność na odświeżenie oraz podstawowe zabezpieczenie wejścia i wyjścia.

## Iteracja 1

Data przebiegu: pierwsza kompletna składka stron, zanim Backend Agent dostał uwagi do zapisu recenzji.

W tej iteracji formularz recenzji był sprawdzany w przeglądarce (`min`/`max` oraz `frontend/js/main.js`). Serwer przyjmował POST i od razu rysował `film.php`. QA odesłał dwie usterki do Backend Agenta. Poprawki nie należały do warstwy CSS ani do skryptu, który zachowywał się zgodnie z opisem.

| ID | Przypadek | Oczekiwany wynik | Wynik |
| --- | --- | --- | --- |
| TC-01 | Wejście na `index.php` | Karty filmów z danymi z bazy | PASS |
| TC-02 | GET `tytul=Incepc` | Na liście zostaje Incepcja | PASS |
| TC-03 | GET `gatunek=Dramat` | Filmy tylko tego gatunku | PASS |
| TC-04 | Średnia na karcie | `ROUND(AVG(ocena), 1)`, odznaka | PASS |
| TC-05 | `film.php?id=1` | Pełne dane Incepcji | PASS |
| TC-06 | Obsada filmu | `INNER JOIN`, link do `aktor.php?id=` | PASS |
| TC-07 | `aktor.php?id=1` | DiCaprio i więcej niż jeden film | PASS |
| TC-08 | POST recenzji z oceną `0` i `11` z pominięciem JS | Odrzucenie, brak INSERT, komunikat o skali 1–10 | **FAIL** |
| TC-09 | Puste pola w JS | Formularz nie schodzi z przeglądarki | PASS |
| TC-10 | F5 po udanym dodaniu recenzji | Brak drugiego wpisu | **FAIL** |
| TC-11 | Treść recenzji z `<script>` | Tekst wyświetlony jako znaki, nie jako kod | PASS |
| TC-12 | Tytuł wyszukiwania ze znakiem `'` | Zapytanie przygotowane, strona działa | PASS |
| TC-13 | `film.php?id=99999` i `id=abc` | Komunikat 404, brak ostrzeżeń SQL | PASS |
| TC-14 | Dodanie filmu i aktora | Rekord w bazie, komunikat po przekierowaniu | PASS |
| TC-15 | Okno węższe niż 800 px | Jedna kolumna kart i formularzy | PASS |

### Usterki przekazane Backend Agentowi

**QA-01. Brak walidacji skali 1–10 po stronie serwera.**  
Kroki: wysłać POST na `film.php?id=1` z polami `autor`, `ocena=11`, `tresc`, bez udziału `main.js`. To samo dla `ocena=0`.  
Wynik iteracji 1: żądanie nie było odrzucane w PHP, zanim powstało polecenie zapisu. Kontrola w HTML i JS nie stanowi walidacji serwera.  
Oczekiwanie: `zapiszRecenzje()` zwraca błąd, wiersz nie powstaje, użytkownik widzi komunikat o zakresie 1–10.

**QA-02. Odświeżenie F5 dubluje recenzję.**  
Kroki: dodać poprawną recenzję i odświeżyć stronę, która odpowiedziała na POST.  
Wynik iteracji 1: przeglądarka ponawia POST, w tabeli `recenzje` są dwa takie same wiersze.  
Oczekiwanie: po sukcesie nagłówek `Location` na adres GET. F5 odświeża GET i nie wstawia kolejnego wiersza.

Inne przypadki w iteracji 1 nie wymagały poprawek. Frontend Agent nie dostał zwrotu.

## Iteracja 2

Data przebiegu: po poprawce Backend Agenta. W kodzie jest `walidujOcene()` z zakresem 1–10 oraz `przekieruj()` wywoływane z `film.php` i `index.php` wyłącznie wtedy, gdy funkcja zapisu zwróci `true`.

| ID | Przypadek | Oczekiwany wynik | Wynik |
| --- | --- | --- | --- |
| TC-01 | Katalog | Karty 8 filmów startowych | PASS |
| TC-02 | Fragment tytułu | Filtr `LIKE` zawęża listę | PASS |
| TC-03 | Gatunek | Lista zgodna z wybraną wartością | PASS |
| TC-04 | Średnia i brak ocen | Wartość z jedną cyfrą po przecinku albo „Brak ocen” dla filmu bez recenzji | PASS |
| TC-05 | Szczegóły filmu | Opis, rok, gatunek, średnia | PASS |
| TC-06 | Obsada | Aktorzy i role, linki do profili | PASS |
| TC-07 | Profil aktora | Notka i filmografia z rolą | PASS |
| TC-08 | Ocena `0`, `11`, `7.5`, tekst | Brak INSERT, komunikat o skali 1–10 | PASS |
| TC-09 | Puste pola | JS blokuje wysyłkę; PHP też odrzuca puste POST | PASS |
| TC-10 | F5 po zapisie | Adres to GET ze `status=recenzja`, liczba wierszy bez zmian | PASS |
| TC-11 | Znaczniki w treści | `htmlspecialchars` przy wypisie | PASS |
| TC-12 | Apostrof w wyszukiwarce | Prepared statement, brak błędu SQL | PASS |
| TC-13 | Zły identyfikator | Strona „Nie znaleziono” | PASS |
| TC-14 | Nowy film, aktor, rola | Zapis i PRG na `index.php?status=` | PASS |
| TC-15 | Wąskie okno | Siatka i formularze w jednej kolumnie | PASS |

Iteracja 2 nie ma otwartych usterek. TC-08 i TC-10, które w iteracji 1 miały status FAIL, po poprawce Backend Agenta mają status PASS.

## Wniosek

Aplikacja przechodzi uzgodniony zestaw. Warunek zaliczenia z `Docs/Specs.md` (9 kryteriów) jest spełniony w kodzie, który wyszedł z iteracji 2, a nie w wersji sprzed uwag QA.
