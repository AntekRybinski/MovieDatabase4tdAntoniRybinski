<?php
declare(strict_types=1);

require_once __DIR__ . '/backend/includes/functions.php';

$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
$id = $id === false ? 0 : (int) $id;
$aktor = pobierzAktora($id);

if ($aktor === null) {
    http_response_code(404);
    naglowek('Nie znaleziono aktora');
    echo '<section class="panel" style="margin-top:1.5rem"><h1>Nie znaleziono aktora</h1>';
    echo '<p>Identyfikator jest nieprawidłowy albo aktor został usunięty.</p>';
    echo '<p><a class="przycisk" href="index.php">Wróć do katalogu</a></p></section>';
    stopka();
    exit;
}

$filmy = pobierzFilmyAktora($id);
$pelneImie = $aktor['imie'] . ' ' . $aktor['nazwisko'];

naglowek($pelneImie);
?>

<p style="margin-top:1.2rem"><a href="index.php">← Katalog</a></p>

<section class="naglowek-strony">
    <div>
        <p class="chip">Aktor</p>
        <h1><?php echo h($pelneImie); ?></h1>
    </div>
</section>

<article class="panel" style="margin-bottom:1rem">
    <h2>Notka</h2>
    <?php if (trim((string) $aktor['opis']) === ''): ?>
        <p class="przygaszony">Brak opisu.</p>
    <?php else: ?>
        <p class="opis"><?php echo nl2br(h((string) $aktor['opis'])); ?></p>
    <?php endif; ?>
</article>

<section class="panel" style="margin-bottom:2rem">
    <h2>Filmy</h2>
    <?php if ($filmy === []): ?>
        <p class="przygaszony">Ten aktor nie jest jeszcze przypisany do żadnego filmu.</p>
    <?php else: ?>
        <ul class="filmografia">
            <?php foreach ($filmy as $film): ?>
                <?php $ocena = formatujOcene($film['srednia']); ?>
                <li>
                    <a class="pozycja-filmu" href="film.php?id=<?php echo (int) $film['id']; ?>">
                        <span>
                            <strong><?php echo h($film['tytul']); ?></strong>
                            <span class="przygaszony">
                                <?php echo (int) $film['rok_produkcji']; ?> · <?php echo h($film['gatunek']); ?> · rola: <?php echo h($film['rola']); ?>
                            </span>
                        </span>
                        <span class="odznaka <?php echo h($ocena['klasa']); ?>"><?php echo h($ocena['tekst']); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<?php
stopka();
