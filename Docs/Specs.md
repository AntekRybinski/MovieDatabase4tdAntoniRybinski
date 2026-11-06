# Specyfikacja — Baza Filmów

## Cel projektu

Celem jest aplikacja internetowa, w której można przeglądać katalog filmów, sprawdzać obsadę i czytać oraz dodawać recenzje. Projekt realizuje wymagania szkolne kwalifikacji INF.03: dynamiczna strona w PHP, relacyjna baza MySQL, formularze, walidacja, bezpieczne wypisywanie danych i czytelny podział na warstwy.

Aplikacja działa lokalnie w XAMPP. Nie wymaga zewnętrznych bibliotek ani frameworka.

## Wymagania funkcjonalne

1. Użytkownik widzi katalog filmów w formie kart z tytułem, rokiem, gatunkiem, skróconym opisem i średnią ocen.
2. Wyszukiwarka filtruje katalog po tytule (fragment, metoda GET) i po gatunku (dokładne dopasowanie, metoda GET).
3. Średnia jest liczona jako `ROUND(AVG(ocena), 1)`. Film bez recenzji pokazuje tekst „Brak ocen”.
4. Karta prowadzi do `film.php?id=X` ze pełnym opisem, średnią, listą aktorów i listą recenzji.
5. Każdy aktor w obsadzie prowadzi do `aktor.php?id=Y`. Profil zawiera dane aktora oraz filmy, w których zagrał, razem z nazwą roli.
6. Na stronie filmu jest formularz recenzji: autor, ocena 1–10, treść.
7. Ocena spoza zakresu, puste pola i zbyt długie teksty są odrzucane. Po udanym zapisie przeglądarka dostaje przekierowanie, a odświeżenie F5 nie dodaje drugiej recenzji.
8. Użytkownik może dodać film, aktora oraz powiązanie w tabeli `obsada`.
9. Baza startowa zawiera co najmniej 6 filmów, 10 aktorów, powiązania obsady i po 2–3 recenzje dla filmów z zestawu testowego.

## Wymagania niefunkcjonalne

1. Kodowanie znaków: UTF-8 w stronach i `utf8mb4` / `utf8mb4_polish_ci` w bazie, żeby polskie litery sortowały się poprawnie.
2. Silnik InnoDB oraz klucze obce `ON DELETE CASCADE`, żeby usunięcie filmu zabierało jego recenzje i role, a usunięcie aktora zabierało jego wpisy w obsadzie.
3. Dane wejściowe nie trafiają do SQL przez sklejenie stringów. Identyfikatory są liczbami, reszta idzie w prepared statements.
4. Dane wyjściowe są kodowane przez `htmlspecialchars`, żeby treść recenzji nie wykonała się jako HTML.
5. Interfejs jest czytelny na wąskim oknie: siatka kart zwija się do jednej kolumny, formularze pozostają dostępne z klawiatury.
6. Aplikacja nie wymaga internetu do czcionek ani obrazów. Działa w lokalnym Apache.
7. Połączenie z bazą jest jawne i zgodne z lokalnym XAMPP: `localhost`, użytkownik `root`, puste hasło, baza `baza_filmow`.

## Kryteria akceptacji

| Nr | Kryterium | Spełnienie |
| --- | --- | --- |
| 1 | Cztery tabele, klucze główne i obce z `ON DELETE CASCADE`, zestaw znaków `utf8mb4` | tak |
| 2 | Dane testowe: minimum 6 filmów, 10 aktorów, obsada, 2–3 recenzje na filmy zestawu | tak |
| 3 | Połączenie `mysqli` o parametrach wymaganych w zadaniu | tak |
| 4 | Katalog, wyszukiwarka GET, średnia `ROUND(AVG(ocena), 1)` i komunikat „Brak ocen” | tak |
| 5 | Szczegóły filmu, obsada (`INNER JOIN`) i profil aktora z rolami | tak |
| 6 | Zapis recenzji z walidacją oceny 1–10 w PHP oraz w JavaScript | tak |
| 7 | Przekierowanie PRG po udanym zapisie | tak |
| 8 | Rzutowanie identyfikatorów, prepared statements, `htmlspecialchars` | tak |
| 9 | Responsywny ciemny interfejs i dokumentacja projektu | tak |

## Podział pracy agentów

| Agent | Zakres | Pliki |
| --- | --- | --- |
| Planning Agent | Cel, wymagania, architektura, przepływy, podział zadań | `Docs/Specs.md`, `Docs/Architecture.md`, `Docs/Workflow.md`, `Docs/Sessions/Planning.md` |
| Data Analyst | Model danych, klucze, reguła oceny 1–10, dane testowe | `database/schema.sql`, `Docs/Sessions/DataAnalyst.md` |
| Backend Agent | Połączenie, zapytania, walidacja, zapis, PRG | `backend/config/db.php`, `backend/includes/functions.php`, logika POST w `index.php`, `film.php`, `aktor.php`, `Docs/Sessions/Backend.md` |
| Frontend Agent | Karty, formularze, ciemny motyw, walidacja w przeglądarce | `frontend/css/style.css`, `frontend/js/main.js`, znaczniki stron, `Docs/Sessions/Frontend.md` |
| QA Agent | Dwie iteracje testów, rejestr usterek i ponowny przebieg | `Docs/QA-Report.md`, `Docs/Sessions/QA.md` |

Sprawozdanie zbiorcze jest w `Docs/Final-Report.md`. Instrukcja uruchomienia jest w `README.md`.

## Poza zakresem

Aplikacja nie ma kont użytkowników, ról administratora ani usuwania rekordów z poziomu strony. Formularze dodawania są otwarte, bo projekt jest lokalnym ćwiczeniem, a nie serwisem publicznym.
