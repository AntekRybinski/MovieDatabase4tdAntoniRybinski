<?php
declare(strict_types=1);

require_once __DIR__ . '/backend/includes/functions.php';

$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
$id = $id === false ? 0 : (int) $id;
$film = pobierzFilm($id);

if ($film === null) {
    http_response_code(404);
    naglowek('Nie znaleziono filmu');
    echo '<section class="panel" style="margin-top:1.5rem"><h1>Nie znaleziono filmu</h1>';
    echo '<p>Identyfikator jest nieprawidłowy albo film został usunięty.</p>';
    echo '<p><a class="przycisk" href="index.php">Wróć do katalogu</a></p></section>';
    stopka();
    exit;
}

$blad = '';
$stare = ['autor' => '', 'ocena' => '', 'tresc' => ''];
$status = tekstWejscia($_GET['status'] ?? '', 20);
$komunikat = $status === 'recenzja' ? 'Recenzja została zapisana.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stare = [
        'autor' => tekstWejscia($_POST['autor'] ?? '', 80),
        'ocena' => tekstWejscia(is_array($_POST['ocena'] ?? null) ? '' : (string) ($_POST['ocena'] ?? ''), 4),
        'tresc' => tekstWejscia($_POST['tresc'] ?? '', 2000),
    ];
    $wynik = zapiszRecenzje($id, $_POST['autor'] ?? '', $_POST['ocena'] ?? '', $_POST['tresc'] ?? '');
    if ($wynik === true) {
        przekieruj('film.php?id=' . $id . '&status=recenzja');
    }
    $blad = (string) $wynik;
    $komunikat = '';
}

$obsada = pobierzObsade($id);
$recenzje = pobierzRecenzje($id);
$ocena = formatujOcene($film['srednia']);

naglowek((string) $film['tytul']);
?>

<p style="margin-top:1.2rem"><a href="index.php">← Katalog</a></p>

<section class="naglowek-strony">
    <div>
        <p class="chip"><?php echo h($film['gatunek']); ?></p>
        <h1><?php echo h($film['tytul']); ?></h1>
        <p class="przygaszony">Rok produkcji: <?php echo (int) $film['rok_produkcji']; ?>
            <?php if ($film['srednia'] !== null): ?>
                · <?php echo h(odmianaRecenzji((int) $film['liczba_ocen'])); ?>
            <?php endif; ?>
        </p>
    </div>
    <span class="odznaka <?php echo h($ocena['klasa']); ?>">Średnia: <?php echo h($ocena['tekst']); ?></span>
</section>

<?php if ($komunikat !== ''): ?>
    <p class="alert alert-ok" role="status"><?php echo h($komunikat); ?></p>
<?php endif; ?>

<div class="uklad-filmu">
    <article class="panel">
        <h2>Opis</h2>
        <p class="opis"><?php echo nl2br(h((string) $film['opis'])); ?></p>
    </article>
    <aside class="panel">
        <h2>Obsada</h2>
        <?php if ($obsada === []): ?>
            <p class="przygaszony">Do tego filmu nie przypisano jeszcze aktorów.</p>
        <?php else: ?>
            <ul class="lista-obsady">
                <?php foreach ($obsada as $osoba): ?>
                    <li>
                        <a class="osoba" href="aktor.php?id=<?php echo (int) $osoba['id']; ?>">
                            <strong><?php echo h($osoba['imie'] . ' ' . $osoba['nazwisko']); ?></strong>
                            <span class="przygaszony"><?php echo h($osoba['rola']); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </aside>
</div>

<section class="panel" style="margin-bottom:1rem">
    <h2>Recenzje</h2>
    <?php if ($recenzje === []): ?>
        <p class="przygaszony">Brak ocen. Dodaj pierwszą recenzję.</p>
    <?php else: ?>
        <ul class="lista-recenzji">
            <?php foreach ($recenzje as $recenzja): ?>
                <?php $odznaka = formatujOcene($recenzja['ocena']); ?>
                <li class="recenzja">
                    <header>
                        <strong><?php echo h($recenzja['autor']); ?></strong>
                        <span class="odznaka <?php echo h($odznaka['klasa']); ?>"><?php echo (int) $recenzja['ocena']; ?>/10</span>
                    </header>
                    <p><?php echo nl2br(h((string) $recenzja['tresc'])); ?></p>
                    <p class="przygaszony"><?php echo h(formatujDate((string) $recenzja['data_dodania'])); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<section class="panel" style="margin-bottom:2rem">
    <h2>Dodaj recenzję</h2>
    <p id="blad-formularza" class="alert alert-blad" role="alert"<?php echo $blad === '' ? ' hidden' : ''; ?>><?php echo h($blad); ?></p>
    <form id="form-recenzja" class="formularz" method="post" action="film.php?id=<?php echo $id; ?>" novalidate>
        <div class="dwie-kolumny">
            <div class="pole">
                <label for="autor">Autor</label>
                <input id="autor" name="autor" maxlength="80" value="<?php echo h($stare['autor']); ?>">
            </div>
            <div class="pole">
                <label for="ocena">Ocena (1–10)</label>
                <input id="ocena" name="ocena" type="number" min="1" max="10" step="1" inputmode="numeric" value="<?php echo h($stare['ocena']); ?>">
            </div>
        </div>
        <div class="pole">
            <label for="tresc">Treść</label>
            <textarea id="tresc" name="tresc" maxlength="2000"><?php echo h($stare['tresc']); ?></textarea>
        </div>
        <button type="submit">Opublikuj recenzję</button>
    </form>
</section>

<?php
stopka();
