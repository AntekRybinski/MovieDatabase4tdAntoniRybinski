# Sprawozdanie końcowe — Baza Filmów

**Kwalifikacja:** INF.03  
**Temat:** aplikacja internetowa do przeglądania filmów, obsady i recenzji  
**Technologie:** PHP, MySQL (mysqli), HTML, CSS, JavaScript  
**Środowisko:** XAMPP (Apache, MySQL, katalog `htdocs/MovieDatabase`)  
**Data zamknięcia:** 6 listopada 2026  
**Zespół:** Planning Agent, Data Analyst, Backend Agent, Frontend Agent, QA Agent

## Co powstało

Lokalna aplikacja „Baza Filmów” z katalogiem, wyszukiwarką, kartą filmu, profilem aktora i recenzjami. Baza `baza_filmow` ma cztery tabele: `filmy`, `aktorzy`, `obsada` (wiele-do-wielu) i `recenzje`. Zestaw startowy liczy 8 filmów, 17 aktorów, 19 ról i 20 recenzji.

Logika jest w `backend/includes/functions.php`. Strony `index.php`, `film.php` i `aktor.php` tylko zbierają żądanie, wołają funkcje i składają HTML. Średnia ocen jest liczona w SQL. Film bez recenzji pokazuje „Brak ocen”. Ocena nowej recenzji musi być liczbą całkowitą od 1 do 10 — sprawdza to przeglądarka i ponownie PHP. Udany zapis kończy się przekierowaniem, więc F5 nie powiela formularza.

## Przebieg

1. Planning Agent zamknął wymagania, diagram architektury, ERD i przepływy.
2. Data Analyst oddał `database/schema.sql` z kluczami `ON DELETE CASCADE`, `CHECK` oceny i danymi testowymi.
3. Backend Agent podłączył `mysqli` i funkcje odczytu oraz zapisu.
4. Frontend Agent złożył ciemny, responsywny interfejs i walidację formularza recenzji.
5. QA Agent w iteracji 1 odrzucił brak serwerowej skali 1–10 oraz duplikat recenzji po F5 i odesłał to do Backend Agenta.
6. Backend Agent dodał `walidujOcene()` oraz nagłówek `Location`.
7. QA Agent w iteracji 2 zamknął zestaw testów statusem PASS.

Szczegóły są w `Docs/QA-Report.md` i w raportach sesji w `Docs/Sessions/`.

## Lista kontrolna

- [x] **1.** Baza `baza_filmow` (`utf8mb4`, `utf8mb4_polish_ci`) oraz 4 tabele z kluczami głównymi i obcymi `ON DELETE CASCADE`.
- [x] **2.** Dane testowe: co najmniej 6 filmów, 10 aktorów, powiązania `obsada`, po 2–3 recenzje.
- [x] **3.** Połączenie `mysqli`: `localhost`, `root`, puste hasło, baza `baza_filmow`, kodowanie `utf8mb4`.
- [x] **4.** Katalog kart, wyszukiwarka GET po tytule i gatunku, średnia `ROUND(AVG(ocena), 1)`, komunikat „Brak ocen”.
- [x] **5.** Szczegóły filmu, obsada przez `INNER JOIN`, profil aktora z filmami i nazwą roli.
- [x] **6.** Formularz recenzji oraz walidacja oceny 1–10 w JavaScript i w PHP.
- [x] **7.** Wzorzec PRG (`header Location`) po dodaniu recenzji, filmu, aktora i roli.
- [x] **8.** Identyfikatory rzutowane na int, prepared statements, `htmlspecialchars` przy wyświetlaniu.
- [x] **9.** Responsywny ciemny motyw, odznaki ocen i dokumentacja (specyfikacja, architektura, przepływy, QA, README).

**Wynik: 9/9.**

## Jak oddać projekt do uruchomienia

Uruchomić Apache i MySQL w XAMPP, zaimportować `database/schema.sql`, otworzyć `http://localhost/MovieDatabase/`. Pełna instrukcja jest w `README.md`.

## Podsumowanie

Projekt spełnia uzgodniony zakres szkolny. Iteracja QA zmieniła sposób zapisu recenzji: skala 1–10 jest egzekwowana na serwerze, a odświeżenie strony nie dokleja kopii wpisu. Aplikacja pozostaje świadomie lokalna — konto `root` bez hasła jest ustawieniem XAMPP z treści zadania i nie jest propozycją konfiguracji serwera dostępnego w sieci.
