# Sesja — Planning Agent

## Zadanie

Rozpisać aplikację „Baza Filmów” tak, żeby dało się ją złożyć w XAMPP bez dopowiadania zakresu w trakcie kodowania. Ustalić kryteria INF.03, podział na pięć ról i kolejność prac.

## Co zostało zrobione

- Spisano cel, wymagania funkcjonalne i niefunkcjonalne w `Docs/Specs.md`.
- Z dziewięciu wymagań z polecenia powstała lista akceptacji 9/9, ta sama, którą później odhacza `Docs/Final-Report.md`.
- W `Docs/Architecture.md` opisano trzy warstwy i dwa diagramy: przepływ od przeglądarki do MySQL oraz ERD czterech tabel.
- W `Docs/Workflow.md` opisano ścieżkę użytkownika (katalog → film → aktor → recenzja → PRG) oraz pętlę QA, w której iteracja 1 wraca do Backend Agenta.
- Ustalono, że strony PHP stoją w katalogu głównym projektu, a CSS i JS w `frontend/`. Dzięki temu adresy z polecenia (`film.php?id=X`, `aktor.php?id=Y`) działają bez przepisywania reguł Apache.
- Ustalono, że dodawanie filmu i aktora nie dostaje osobnych plików. Formularze są sekcją `index.php#dodaj`, bo lista plików była zamknięta, a funkcja i tak należy do wymagań.

## Problemy

Pierwsza wersja podziału chciała przenieść cały HTML do katalogu `frontend/`. To zepsułoby krótkie adresy wymagane w treści zadania i wymusiłoby dodatkowe przekierowania. Zostawiono strony w korzeniu, a warstwę prezentacji wydzielono odpowiedzialnością: SQL tylko w `functions.php`, znaczniki i styl na stronach oraz w `frontend/`.

Druga niejasność dotyczyła „Brak ocen”. Da się ją pokazać na filmie dopisanym formularzem, który nie ma jeszcze recenzji. Filmy z `schema.sql` mają po 2–3 opinie, zgodnie z minimum danych testowych. Planning nie kazał zostawiać pustego rekordu w seedzie.

## Wynik sesji

Zespół dostał zamknięty zakres, mapę plików i dwa miejsca, w których QA miało prawo odesłać pracę: walidacja oceny po stronie serwera oraz brak duplikatu po F5. Obie te kontrole są w kryteriach 6 i 7.
