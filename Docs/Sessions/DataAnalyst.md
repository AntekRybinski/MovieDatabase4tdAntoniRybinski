# Sesja — Data Analyst

## Zadanie

Zaprojektować bazę `baza_filmow` i wypełnić ją danymi, na których da się pokazać relację wiele-do-wielu, średnią ocen i kaskadowe usuwanie.

## Co zostało zrobione

Plik `database/schema.sql`:

- tworzy bazę, jeśli nie istnieje, z `utf8mb4` i `utf8mb4_polish_ci`,
- ustawia `USE baza_filmow` i `SET NAMES utf8mb4`,
- przed ponownym importem usuwa tabele w kolejności dzieci → rodzice (`recenzje`, `obsada`, `filmy`, `aktorzy`),
- tworzy cztery tabele InnoDB.

Kolumny:

| Tabela | Kolumny |
| --- | --- |
| `filmy` | `id`, `tytul`, `rok_produkcji`, `gatunek`, `opis` |
| `aktorzy` | `id`, `imie`, `nazwisko`, `opis` |
| `obsada` | `id`, `film_id`, `aktor_id`, `rola` |
| `recenzje` | `id`, `film_id`, `autor`, `ocena`, `tresc`, `data_dodania` |

Klucze obce:

- `obsada.film_id` → `filmy.id`, `ON DELETE CASCADE`,
- `obsada.aktor_id` → `aktorzy.id`, `ON DELETE CASCADE`,
- `recenzje.film_id` → `filmy.id`, `ON DELETE CASCADE`.

`ocena` jest typu `INT NOT NULL` i ma `CHECK (ocena BETWEEN 1 AND 10)`. `data_dodania` ma `DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP`.

Dane testowe:

- 8 filmów (Incepcja, Skazani na Shawshank, Ojciec chrzestny, Interstellar, Pulp Fiction, Drużyna Pierścienia, Whiplash, Django),
- 17 aktorów,
- 19 powiązań w `obsada`, w tym podwójne obsadzenie Leonarda DiCaprio (Incepcja i Django) oraz Samuela L. Jacksona (Pulp Fiction i Django), żeby związek wiele-do-wielu było widać na profilu aktora,
- 20 recenzji: po dwa albo trzy wpisy na każdy film zestawu, z jawną datą.

Na końcu skryptu ustawiono `AUTO_INCREMENT`, żeby kolejny film dodany z formularza nie wpadł w kolizję z identyfikatorami wpisanymi ręcznie.

## Problemy

Sortowanie polskich tytułów przy domyślnym `utf8mb4_general_ci` potrafi zepchnąć znaki diakrytyczne w inne miejsce niż w słowniku. Dlatego cała baza dostała `utf8mb4_polish_ci`, a połączenie PHP ustawia `utf8mb4`.

Sama tabela `recenzje` nie powstrzyma oceny 0 albo 15 na starszym serwerze, który zignoruje `CHECK`. Ograniczenie zostało w schemacie, a Backend Agent i tak odrzuca taką wartość w PHP. To była świadoma decyzja, nie luka zostawiona QA.

Unikalność `(film_id, aktor_id, rola)` blokuje przypadkowe podwójne kliknięcie „Zapisz rolę”. Ten sam aktor może mieć w filmie drugą rolę, jeśli nazwa jest inna.

## Wynik sesji

Schemat da się zaimportować jednym poleceniem i uruchomić ponownie bez ręcznego sprzątania tabel. Zestaw przekracza minima: 6 filmów, 10 aktorów, obsada, 2–3 recenzje.
