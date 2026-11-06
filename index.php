<?php
declare(strict_types=1);

require_once __DIR__ . '/backend/includes/functions.php';

$tytulSzukany = tekstWejscia($_GET['tytul'] ?? '', 100);
$gatunekSzukany = tekstWejscia($_GET['gatunek'] ?? '', 80);
$status = tekstWejscia($_GET['status'] ?? '', 20);

$komunikaty = [
    'film' => 'Nowy film został zapisany w katalogu.',
    'aktor' => 'Nowy aktor został dodany.',
    'obsada' => 'Rola została przypisana do obsady.',
];
$komunikat = $komunikaty[$status] ?? '';

$blad = '';
$panel = '';
$filmForm = ['tytul' => '', 'rok_produkcji' => '', 'gatunek' => '', 'opis' => ''];
$aktorForm = ['imie' => '', 'nazwisko' => '', 'opis' => ''];
$rolaForm = ['film_id' => '', 'aktor_id' => '', 'rola' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcja = tekstWejscia($_POST['akcja'] ?? '', 20);

    if ($akcja === 'film') {
        $filmForm = [
            'tytul' => tekstWejscia($_POST['tytul'] ?? '', 200),
            'rok_produkcji' => tekstWejscia((string) ($_POST['rok_produkcji'] ?? ''), 4),
            'gatunek' => tekstWejscia($_POST['gatunek'] ?? '', 80),
            'opis' => tekstWejscia($_POST['opis'] ?? '', 5000),
        ];
        $wynik = zapiszFilm(
            $_POST['tytul'] ?? '',
            $_POST['rok_produkcji'] ?? '',
            $_POST['gatunek'] ?? '',
            $_POST['opis'] ?? ''
        );
        if ($wynik === true) {
            przekieruj('index.php?status=film');
        }
        $blad = (string) $wynik;
        $panel = 'film';
    } elseif ($akcja === 'aktor') {
        $aktorForm = [
            'imie' => tekstWejscia($_POST['imie'] ?? '', 80),
            'nazwisko' => tekstWejscia($_POST['nazwisko'] ?? '', 100),
            'opis' => tekstWejscia($_POST['opis'] ?? '', 5000),
        ];
        $wynik = zapiszAktora($_POST['imie'] ?? '', $_POST['nazwisko'] ?? '', $_POST['opis'] ?? '');
        if ($wynik === true) {
            przekieruj('index.php?status=aktor');
        }
        $blad = (string) $wynik;
        $panel = 'aktor';
    } elseif ($akcja === 'obsada') {
        $rolaForm = [
            'film_id' => tekstWejscia((string) ($_POST['film_id'] ?? ''), 10),
            'aktor_id' => tekstWejscia((string) ($_POST['aktor_id'] ?? ''), 10),
            'rola' => tekstWejscia($_POST['rola'] ?? '', 150),
        ];
        $wynik = zapiszRole($_POST['film_id'] ?? '', $_POST['aktor_id'] ?? '', $_POST['rola'] ?? '');
        if ($wynik === true) {
            przekieruj('index.php?status=obsada');
        }
        $blad = (string) $wynik;
        $panel = 'obsada';
    } else {
        $blad = 'Nieznana operacja formularza.';
    }
}

$filmy = pobierzFilmy($tytulSzukany, $gatunekSzukany);
$gatunki = pobierzGatunki();
$wszystkieFilmy = pobierzFilmy('', '');
$aktorzy = pobierzAktorow();

naglowek('Katalog filmów', 'katalog');
?>

<section class="bohater">
    <p class="chip">INF.03 · PHP · MySQL</p>
    <h1>Katalog, który da się przejrzeć w jeden wieczór.</h1>
    <p class="lead">Szukaj po tytule i gatunku, sprawdzaj średnią ocen i przechodź do obsady oraz recenzji.</p>
</section>

<?php if ($komunikat !== ''): ?>
    <p class="alert alert-ok" role="status"><?php echo h($komunikat); ?></p>
<?php endif; ?>
<?php if ($blad !== ''): ?>
    <p class="alert alert-blad" role="alert"><?php echo h($blad); ?></p>
<?php endif; ?>

<form class="panel wyszukiwarka" method="get" action="index.php">
    <div class="pole">
        <label for="tytul">Tytuł</label>
        <input id="tytul" name="tytul" type="search" maxlength="100" value="<?php echo h($tytulSzukany); ?>" placeholder="np. Incepcja">
    </div>
    <div class="pole">
        <label for="gatunek">Gatunek</label>
        <select id="gatunek" name="gatunek">
            <option value="">Wszystkie gatunki</option>
            <?php foreach ($gatunki as $gatunek): ?>
                <option value="<?php echo h($gatunek); ?>"<?php echo $gatunek === $gatunekSzukany ? ' selected' : ''; ?>>
                    <?php echo h($gatunek); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit">Szukaj</button>
</form>

<p class="przygaszony">Znaleziono: <?php echo count($filmy); ?></p>

<?php if ($filmy === []): ?>
    <p class="pusty">Brak filmów spełniających kryteria wyszukiwania.</p>
<?php else: ?>
    <section class="siatka" aria-label="Lista filmów">
        <?php foreach ($filmy as $film): ?>
            <?php $ocena = formatujOcene($film['srednia']); ?>
            <article class="karta">
                <div class="meta">
                    <span class="chip"><?php echo h($film['gatunek']); ?></span>
                    <span class="odznaka <?php echo h($ocena['klasa']); ?>"><?php echo h($ocena['tekst']); ?></span>
                </div>
                <h2><?php echo h($film['tytul']); ?></h2>
                <p class="przygaszony"><?php echo (int) $film['rok_produkcji']; ?> · <?php echo $film['srednia'] === null ? 'brak recenzji' : h(odmianaRecenzji((int) $film['liczba_ocen'])); ?></p>
                <p><?php echo h(skroc((string) $film['opis'])); ?></p>
                <a class="przycisk" href="film.php?id=<?php echo (int) $film['id']; ?>">Szczegóły</a>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<section id="dodaj">
    <div class="naglowek-strony">
        <div>
            <h2>Dodaj do bazy</h2>
            <p class="przygaszony">Film, aktor albo rola w obsadzie. Po zapisie strona wraca adresem GET, więc odświeżenie nie dubluje wpisu.</p>
        </div>
    </div>
    <div class="panele">
        <details<?php echo $panel === 'film' ? ' open' : ''; ?>>
            <summary>Nowy film</summary>
            <form class="formularz" method="post" action="index.php#dodaj">
                <input type="hidden" name="akcja" value="film">
                <div class="dwie-kolumny">
                    <div class="pole">
                        <label for="nowy-tytul">Tytuł</label>
                        <input id="nowy-tytul" name="tytul" maxlength="200" required value="<?php echo h($filmForm['tytul']); ?>">
                    </div>
                    <div class="pole">
                        <label for="rok">Rok produkcji</label>
                        <input id="rok" name="rok_produkcji" type="number" min="1888" max="<?php echo (int) date('Y') + 1; ?>" required value="<?php echo h($filmForm['rok_produkcji']); ?>">
                    </div>
                </div>
                <div class="pole">
                    <label for="nowy-gatunek">Gatunek</label>
                    <input id="nowy-gatunek" name="gatunek" maxlength="80" required value="<?php echo h($filmForm['gatunek']); ?>" placeholder="np. Dramat">
                </div>
                <div class="pole">
                    <label for="opis-filmu">Opis</label>
                    <textarea id="opis-filmu" name="opis" maxlength="5000"><?php echo h($filmForm['opis']); ?></textarea>
                </div>
                <button type="submit">Zapisz film</button>
            </form>
        </details>

        <details<?php echo $panel === 'aktor' ? ' open' : ''; ?>>
            <summary>Nowy aktor</summary>
            <form class="formularz" method="post" action="index.php#dodaj">
                <input type="hidden" name="akcja" value="aktor">
                <div class="dwie-kolumny">
                    <div class="pole">
                        <label for="imie">Imię</label>
                        <input id="imie" name="imie" maxlength="80" required value="<?php echo h($aktorForm['imie']); ?>">
                    </div>
                    <div class="pole">
                        <label for="nazwisko">Nazwisko</label>
                        <input id="nazwisko" name="nazwisko" maxlength="100" required value="<?php echo h($aktorForm['nazwisko']); ?>">
                    </div>
                </div>
                <div class="pole">
                    <label for="opis-aktora">Opis</label>
                    <textarea id="opis-aktora" name="opis" maxlength="5000"><?php echo h($aktorForm['opis']); ?></textarea>
                </div>
                <button type="submit">Zapisz aktora</button>
            </form>
        </details>

        <details<?php echo $panel === 'obsada' ? ' open' : ''; ?>>
            <summary>Przypisz rolę</summary>
            <form class="formularz" method="post" action="index.php#dodaj">
                <input type="hidden" name="akcja" value="obsada">
                <div class="dwie-kolumny">
                    <div class="pole">
                        <label for="film_id">Film</label>
                        <select id="film_id" name="film_id" required>
                            <option value="">Wybierz film</option>
                            <?php foreach ($wszystkieFilmy as $film): ?>
                                <option value="<?php echo (int) $film['id']; ?>"<?php echo (string) $film['id'] === $rolaForm['film_id'] ? ' selected' : ''; ?>>
                                    <?php echo h($film['tytul']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="pole">
                        <label for="aktor_id">Aktor</label>
                        <select id="aktor_id" name="aktor_id" required>
                            <option value="">Wybierz aktora</option>
                            <?php foreach ($aktorzy as $aktor): ?>
                                <option value="<?php echo (int) $aktor['id']; ?>"<?php echo (string) $aktor['id'] === $rolaForm['aktor_id'] ? ' selected' : ''; ?>>
                                    <?php echo h($aktor['nazwisko'] . ' ' . $aktor['imie']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="pole">
                    <label for="rola">Rola</label>
                    <input id="rola" name="rola" maxlength="150" required value="<?php echo h($rolaForm['rola']); ?>">
                </div>
                <button type="submit">Zapisz rolę</button>
            </form>
        </details>
    </div>
</section>

<?php
stopka();
