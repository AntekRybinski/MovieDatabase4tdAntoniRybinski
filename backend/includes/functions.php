<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

function polaczenie(): mysqli
{
    global $polaczenie;
    return $polaczenie;
}

function h(?string $tekst): string
{
    return htmlspecialchars((string) $tekst, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function tekstWejscia($wartosc, int $maks = 200): string
{
    if (!is_string($wartosc)) {
        return '';
    }

    $wartosc = trim($wartosc);
    if (mb_strlen($wartosc) > $maks) {
        $wartosc = mb_substr($wartosc, 0, $maks);
    }

    return $wartosc;
}

function wzorzecLike(string $fraza): string
{
    $fraza = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $fraza);
    return '%' . $fraza . '%';
}

function powiazParametry(mysqli_stmt $zapytanie, string $typy, array $parametry): void
{
    $powiazania = [$typy];
    foreach ($parametry as &$wartosc) {
        $powiazania[] = &$wartosc;
    }
    unset($wartosc);
    $zapytanie->bind_param(...$powiazania);
}

function wykonajZapytanie(string $sql, string $typy = '', array $parametry = []): array
{
    $zapytanie = polaczenie()->prepare($sql);
    if ($typy !== '') {
        powiazParametry($zapytanie, $typy, $parametry);
    }
    $zapytanie->execute();
    $rezultat = $zapytanie->get_result();
    $wiersze = $rezultat ? $rezultat->fetch_all(MYSQLI_ASSOC) : [];
    $zapytanie->close();

    return $wiersze;
}

function pobierzJeden(string $sql, string $typy = '', array $parametry = []): ?array
{
    $wiersze = wykonajZapytanie($sql, $typy, $parametry);
    return $wiersze[0] ?? null;
}

function wykonajZapis(string $sql, string $typy, array $parametry): void
{
    $zapytanie = polaczenie()->prepare($sql);
    powiazParametry($zapytanie, $typy, $parametry);
    $zapytanie->execute();
    $zapytanie->close();
}

function przekieruj(string $adres): void
{
    header('Location: ' . $adres);
    exit;
}

function formatujOcene($srednia): array
{
    if ($srednia === null || $srednia === '') {
        return ['tekst' => 'Brak ocen', 'klasa' => 'ocena-brak'];
    }

    $wartosc = (float) $srednia;
    $klasa = 'ocena-niska';
    if ($wartosc >= 8) {
        $klasa = 'ocena-wysoka';
    } elseif ($wartosc >= 5) {
        $klasa = 'ocena-srednia';
    }

    return [
        'tekst' => number_format($wartosc, 1, ',', ''),
        'klasa' => $klasa,
    ];
}

function skroc(string $tekst, int $dlugosc = 160): string
{
    if (mb_strlen($tekst) <= $dlugosc) {
        return $tekst;
    }

    return rtrim(mb_substr($tekst, 0, $dlugosc - 1)) . '…';
}

function odmianaRecenzji(int $liczba): string
{
    $reszta10 = $liczba % 10;
    $reszta100 = $liczba % 100;
    if ($liczba === 1) {
        return '1 recenzja';
    }
    if ($reszta10 >= 2 && $reszta10 <= 4 && ($reszta100 < 12 || $reszta100 > 14)) {
        return $liczba . ' recenzje';
    }

    return $liczba . ' recenzji';
}

function formatujDate(?string $data): string
{
    if ($data === null || $data === '') {
        return '';
    }

    $znacznik = strtotime($data);
    if ($znacznik === false) {
        return '';
    }

    return date('d.m.Y H:i', $znacznik);
}

function pobierzFilmy(string $tytul, string $gatunek): array
{
    return wykonajZapytanie(
        'SELECT f.id, f.tytul, f.rok_produkcji, f.gatunek, f.opis,
                ROUND(AVG(r.ocena), 1) AS srednia,
                COUNT(r.id) AS liczba_ocen
         FROM filmy f
         LEFT JOIN recenzje r ON r.film_id = f.id
         WHERE f.tytul LIKE ?
           AND (? = "" OR f.gatunek = ?)
         GROUP BY f.id, f.tytul, f.rok_produkcji, f.gatunek, f.opis
         ORDER BY f.tytul ASC',
        'sss',
        [wzorzecLike($tytul), $gatunek, $gatunek]
    );
}

function pobierzGatunki(): array
{
    $wiersze = wykonajZapytanie(
        'SELECT DISTINCT gatunek FROM filmy ORDER BY gatunek ASC'
    );

    return array_column($wiersze, 'gatunek');
}

function pobierzFilm(int $id): ?array
{
    if ($id <= 0) {
        return null;
    }

    return pobierzJeden(
        'SELECT f.id, f.tytul, f.rok_produkcji, f.gatunek, f.opis,
                ROUND(AVG(r.ocena), 1) AS srednia,
                COUNT(r.id) AS liczba_ocen
         FROM filmy f
         LEFT JOIN recenzje r ON r.film_id = f.id
         WHERE f.id = ?
         GROUP BY f.id, f.tytul, f.rok_produkcji, f.gatunek, f.opis',
        'i',
        [$id]
    );
}

function pobierzObsade(int $filmId): array
{
    if ($filmId <= 0) {
        return [];
    }

    return wykonajZapytanie(
        'SELECT a.id, a.imie, a.nazwisko, o.rola
         FROM obsada o
         INNER JOIN aktorzy a ON a.id = o.aktor_id
         WHERE o.film_id = ?
         ORDER BY a.nazwisko ASC, a.imie ASC',
        'i',
        [$filmId]
    );
}

function pobierzRecenzje(int $filmId): array
{
    if ($filmId <= 0) {
        return [];
    }

    return wykonajZapytanie(
        'SELECT id, autor, ocena, tresc, data_dodania
         FROM recenzje
         WHERE film_id = ?
         ORDER BY data_dodania DESC, id DESC',
        'i',
        [$filmId]
    );
}

function pobierzAktorow(): array
{
    return wykonajZapytanie(
        'SELECT id, imie, nazwisko FROM aktorzy ORDER BY nazwisko ASC, imie ASC'
    );
}

function pobierzAktora(int $id): ?array
{
    if ($id <= 0) {
        return null;
    }

    return pobierzJeden(
        'SELECT id, imie, nazwisko, opis FROM aktorzy WHERE id = ?',
        'i',
        [$id]
    );
}

function pobierzFilmyAktora(int $aktorId): array
{
    if ($aktorId <= 0) {
        return [];
    }

    return wykonajZapytanie(
        'SELECT f.id, f.tytul, f.rok_produkcji, f.gatunek, o.rola,
                ROUND(AVG(r.ocena), 1) AS srednia
         FROM obsada o
         INNER JOIN filmy f ON f.id = o.film_id
         LEFT JOIN recenzje r ON r.film_id = f.id
         WHERE o.aktor_id = ?
         GROUP BY f.id, f.tytul, f.rok_produkcji, f.gatunek, o.rola
         ORDER BY f.rok_produkcji DESC, f.tytul ASC',
        'i',
        [$aktorId]
    );
}

function walidujOcene($ocena): int
{
    $wynik = filter_var($ocena, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 10],
    ]);

    if ($wynik === false) {
        return 0;
    }

    return (int) $wynik;
}

function zapiszRecenzje(int $filmId, $autor, $ocena, $tresc)
{
    if ($filmId <= 0 || pobierzFilm($filmId) === null) {
        return 'Wybrany film nie istnieje.';
    }
    if (!is_string($autor) || !is_string($tresc) || is_array($ocena)) {
        return 'Nieprawidłowe dane formularza.';
    }

    $autor = trim($autor);
    $tresc = trim($tresc);
    if ($autor === '' || $tresc === '') {
        return 'Autor i treść recenzji są wymagane.';
    }
    if (mb_strlen($autor) > 80) {
        return 'Autor może mieć najwyżej 80 znaków.';
    }
    if (mb_strlen($tresc) > 2000) {
        return 'Treść recenzji może mieć najwyżej 2000 znaków.';
    }

    $ocenaInt = walidujOcene($ocena);
    if ($ocenaInt < 1 || $ocenaInt > 10) {
        return 'Ocena musi być liczbą całkowitą od 1 do 10.';
    }

    try {
        wykonajZapis(
            'INSERT INTO recenzje (film_id, autor, ocena, tresc) VALUES (?, ?, ?, ?)',
            'isis',
            [$filmId, $autor, $ocenaInt, $tresc]
        );
    } catch (mysqli_sql_exception $wyjatek) {
        return 'Nie udało się zapisać recenzji.';
    }

    return true;
}

function zapiszFilm($tytul, $rok, $gatunek, $opis)
{
    if (!is_string($tytul) || !is_string($gatunek) || !is_string($opis) || is_array($rok)) {
        return 'Nieprawidłowe dane formularza.';
    }

    $tytul = trim($tytul);
    $gatunek = trim($gatunek);
    $opis = trim($opis);
    $rokInt = filter_var($rok, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1888, 'max_range' => (int) date('Y') + 1],
    ]);

    if ($tytul === '' || mb_strlen($tytul) > 200) {
        return 'Podaj tytuł filmu (maks. 200 znaków).';
    }
    if ($gatunek === '' || mb_strlen($gatunek) > 80) {
        return 'Podaj gatunek (maks. 80 znaków).';
    }
    if (mb_strlen($opis) > 5000) {
        return 'Opis może mieć najwyżej 5000 znaków.';
    }
    if ($rokInt === false) {
        return 'Rok produkcji musi mieścić się w zakresie od 1888 do ' . ((int) date('Y') + 1) . '.';
    }

    try {
        wykonajZapis(
            'INSERT INTO filmy (tytul, rok_produkcji, gatunek, opis) VALUES (?, ?, ?, ?)',
            'siss',
            [$tytul, (int) $rokInt, $gatunek, $opis]
        );
    } catch (mysqli_sql_exception $wyjatek) {
        return 'Nie udało się zapisać filmu.';
    }

    return true;
}

function zapiszAktora($imie, $nazwisko, $opis)
{
    if (!is_string($imie) || !is_string($nazwisko) || !is_string($opis)) {
        return 'Nieprawidłowe dane formularza.';
    }

    $imie = trim($imie);
    $nazwisko = trim($nazwisko);
    $opis = trim($opis);

    if ($imie === '' || mb_strlen($imie) > 80) {
        return 'Podaj imię aktora (maks. 80 znaków).';
    }
    if ($nazwisko === '' || mb_strlen($nazwisko) > 100) {
        return 'Podaj nazwisko aktora (maks. 100 znaków).';
    }
    if (mb_strlen($opis) > 5000) {
        return 'Opis może mieć najwyżej 5000 znaków.';
    }

    try {
        wykonajZapis(
            'INSERT INTO aktorzy (imie, nazwisko, opis) VALUES (?, ?, ?)',
            'sss',
            [$imie, $nazwisko, $opis]
        );
    } catch (mysqli_sql_exception $wyjatek) {
        return 'Nie udało się zapisać aktora.';
    }

    return true;
}

function zapiszRole($filmId, $aktorId, $rola)
{
    $film = filter_var($filmId, FILTER_VALIDATE_INT);
    $aktor = filter_var($aktorId, FILTER_VALIDATE_INT);
    if ($film === false || $aktor === false || !is_string($rola)) {
        return 'Nieprawidłowe dane formularza.';
    }

    $film = (int) $film;
    $aktor = (int) $aktor;
    $rola = trim($rola);

    if (pobierzFilm($film) === null) {
        return 'Wybrany film nie istnieje.';
    }
    if (pobierzAktora($aktor) === null) {
        return 'Wybrany aktor nie istnieje.';
    }
    if ($rola === '' || mb_strlen($rola) > 150) {
        return 'Podaj nazwę roli (maks. 150 znaków).';
    }

    $duplikat = pobierzJeden(
        'SELECT id FROM obsada WHERE film_id = ? AND aktor_id = ? AND rola = ?',
        'iis',
        [$film, $aktor, $rola]
    );
    if ($duplikat !== null) {
        return 'Ten aktor ma już przypisaną taką rolę w wybranym filmie.';
    }

    try {
        wykonajZapis(
            'INSERT INTO obsada (film_id, aktor_id, rola) VALUES (?, ?, ?)',
            'iis',
            [$film, $aktor, $rola]
        );
    } catch (mysqli_sql_exception $wyjatek) {
        return 'Nie udało się zapisać roli.';
    }

    return true;
}

function naglowek(string $tytul, string $aktywna = ''): void
{
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=UTF-8');
    }

    $klasaKatalog = $aktywna === 'katalog' ? 'aktywny' : '';
    $klasaDodaj = $aktywna === 'dodaj' ? 'aktywny' : '';
    ?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($tytul); ?> | Baza Filmów</title>
    <link rel="stylesheet" href="frontend/css/style.css">
</head>
<body>
    <a class="pomin" href="#tresc">Przejdź do treści</a>
    <header class="pasek">
        <div class="szeroka pasek-wiersz">
            <a class="znak" href="index.php">Baza Filmów</a>
            <nav class="menu" aria-label="Główne">
                <a class="<?php echo h($klasaKatalog); ?>" href="index.php">Katalog</a>
                <a class="<?php echo h($klasaDodaj); ?>" href="index.php#dodaj">Dodaj</a>
            </nav>
        </div>
    </header>
    <main id="tresc" class="szeroka">
    <?php
}

function stopka(): void
{
    ?>
    </main>
    <footer class="stopka">
        <div class="szeroka">Projekt szkolny INF.03 — aplikacja Baza Filmów (PHP, MySQL).</div>
    </footer>
    <script src="frontend/js/main.js"></script>
</body>
</html>
    <?php
}
