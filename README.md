# Baza Filmów

Aplikacja internetowa do katalogowania filmów, obsady i recenzji. Projekt szkolny w standardzie kwalifikacji INF.03, napisany w PHP i MySQL, uruchamiany lokalnie w XAMPP.

## Co potrafi aplikacja

- pokazuje katalog filmów w postaci kart ze średnią ocen,
- wyszukuje filmy po tytule i gatunku (parametry GET),
- wyświetla szczegóły filmu, obsadę i recenzje,
- prowadzi do profilu aktora i jego filmografii,
- zapisuje nową recenzję z walidacją oceny 1–10,
- pozwala dodać film, aktora i rolę w obsadzie,
- po udanym zapisie robi przekierowanie PRG, więc odświeżenie strony nie dubluje wpisu.

## Architektura

Aplikacja jest podzielona na trzy warstwy:

1. **Prezentacja** — `index.php`, `film.php`, `aktor.php`, `frontend/css/style.css`, `frontend/js/main.js`.
2. **Logika** — `backend/includes/functions.php` (zapytania, walidacja, wspólny nagłówek i stopka).
3. **Dane** — baza `baza_filmow` opisana w `database/schema.sql`.

Przeglądarka wysyła żądanie do skryptu PHP. Skrypt woła funkcje logiki, a te przez `mysqli` i przygotowane zapytania czytają albo zapisują dane w MySQL. Średnia ocen jest liczona w SQL: `ROUND(AVG(ocena), 1)`. Gdy film nie ma recenzji, interfejs pokazuje „Brak ocen”.

Diagramy architektury, ERD i przepływów są w `Docs/Architecture.md` oraz `Docs/Workflow.md`.

## Uruchomienie w XAMPP

1. Skopiuj katalog projektu do `C:\xampp\htdocs\MovieDatabase` (jeśli jeszcze go tam nie ma).
2. W panelu XAMPP uruchom usługi **Apache** i **MySQL**.
3. Zaimportuj schemat razem z danymi testowymi. W phpMyAdmin wybierz Importuj i wskaż plik `database/schema.sql`.

   Alternatywnie z konsoli:

   ```bat
   C:\xampp\mysql\bin\mysql.exe -u root < C:\xampp\htdocs\MovieDatabase\database\schema.sql
   ```

4. Połączenie jest ustawione w `backend/config/db.php`: host `localhost`, użytkownik `root`, puste hasło, baza `baza_filmow`, kodowanie `utf8mb4`. Takie konto jest domyślne w lokalnym XAMPP i nie nadaje się na serwer publiczny.
5. Otwórz w przeglądarce: [http://localhost/MovieDatabase/](http://localhost/MovieDatabase/)

Skrypt `schema.sql` tworzy bazę, jeśli jej nie ma, i przy ponownym imporcie odtwarza tabele od zera.

## Mapa plików

| Ścieżka | Rola |
| --- | --- |
| `index.php` | Katalog, wyszukiwarka, formularze dodawania |
| `film.php` | Szczegóły filmu, obsada, recenzje, formularz oceny |
| `aktor.php` | Profil aktora i lista ról |
| `backend/config/db.php` | Połączenie mysqli |
| `backend/includes/functions.php` | Zapytania, walidacja, szablon strony |
| `frontend/css/style.css` | Ciemny, responsywny motyw |
| `frontend/js/main.js` | Walidacja formularza recenzji |
| `database/schema.sql` | Tabele, klucze, dane testowe |
| `Docs/` | Specyfikacja, architektura, przebieg pracy, raport QA |

## Bezpieczeństwo danych w zakresie projektu

- identyfikatory z adresu są rzutowane na liczby całkowite,
- zapytania z danymi użytkownika idą przez przygotowane instrukcje (`prepare` / `bind_param`),
- tekst wypisywany w HTML przechodzi przez `htmlspecialchars`,
- ocena recenzji jest sprawdzana po stronie JavaScript i ponownie w PHP (zakres 1–10),
- udany zapis kończy się nagłówkiem `Location` (wzorzec POST-redirect-GET).
