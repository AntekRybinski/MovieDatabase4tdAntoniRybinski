# Przepływ pracy — Baza Filmów

## Przeglądanie filmu i dodanie recenzji

Użytkownik zaczyna w katalogu. Wyszukiwarka dokleja do adresu `tytul` i `gatunek`, więc wynik da się skopiować albo odświeżyć bez ponownego wysyłania formularza. Z karty przechodzi do filmu. Stamtąd może zejść do profilu aktora i wrócić albo zostać przy recenzjach.

Formularz recenzji ma atrybut `novalidate`, żeby kontrolę zakresu pokazał własny skrypt, a nie wyłącznie wbudowany komunikat przeglądarki. Skrypt sprawdza puste pola i całkowitą ocenę od 1 do 10. To nie zastępuje serwera: PHP powtarza te warunki, bo żądanie da się wysłać z pominięciem JavaScript.

Gdy zapis się uda, odpowiedź nie jest stroną wyrenderowaną na POST. Jest przekierowaniem na adres filmu ze `status=recenzja`. Kolejne F5 powtarza GET. Wpis powstaje raz.

```mermaid
flowchart TD
    start[Wejscie na index.php]
    szukaj[GET: tytul i gatunek]
    karty[Karty filmow ze srednia]
    film[film.php szczegoly i obsada]
    aktor[aktor.php profil i role]
    formularz[Formularz recenzji]
    js{JS: pola niepuste i ocena 1-10?}
    php{PHP: prepared statement i ocena 1-10?}
    zapis[INSERT do recenzje]
    prg[Location na film.php?id oraz status]
    blad[Komunikat bledu przy formularzu]

    start --> szukaj
    szukaj --> karty
    karty --> film
    film --> aktor
    aktor --> film
    film --> formularz
    formularz --> js
    js -->|nie| blad
    blad --> formularz
    js -->|tak| php
    php -->|nie| blad
    php -->|tak| zapis
    zapis --> prg
    prg --> film
```

Dodanie filmu, aktora i roli idzie tą samą ścieżką PRG, tylko zostaje na `index.php` i rozróżnia sukces parametrami `status=film`, `status=aktor` albo `status=obsada`.

## Praca agentów i pętla QA

Planning Agent spisał zakres i kryteria, zanim powstały tabele. Data Analyst oddał model i dane. Backend Agent podłączył PHP do MySQL i wystawił funkcje. Frontend Agent złożył na tym karty, formularze i walidację w przeglądarce.

QA Agent nie kończy pracy przy pierwszym przejściu. W iteracji 1 dwie usterki wróciły do Backend Agenta: serwer przyjmował ocenę spoza skali 1–10, gdy żądanie omijało przeglądarkę, a odpowiedź na POST renderowała formularz ponownie, więc F5 potrafiło wysłać recenzję drugi raz. Po poprawce (walidacja w `zapiszRecenzje()` oraz `header('Location: ...')`) QA powtórzył zestaw. Iteracja 2 zamknęła się statusem PASS.

```mermaid
flowchart TD
    plan[Planning Agent: specyfikacja i diagramy]
    dane[Data Analyst: schema.sql]
    backend[Backend Agent: db.php i functions.php]
    frontend[Frontend Agent: strony CSS i JS]
    qa[QA Agent: przebieg testow]
    decyzja{Wynik iteracji}
    poprawka[Backend Agent: walidacja 1-10 i PRG]
    qa2[QA Agent: iteracja 2]
    koniec{Wszystkie testy PASS?}
    raport[Final-Report 9 z 9]

    plan --> dane
    dane --> backend
    backend --> frontend
    frontend --> qa
    qa --> decyzja
    decyzja -->|iteracja 1: FAIL| poprawka
    poprawka --> qa2
    qa2 --> koniec
    koniec -->|tak| raport
```
