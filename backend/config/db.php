<?php
declare(strict_types=1);

$host = 'localhost';
$user = 'root';
$haslo = '';
$baza = 'baza_filmow';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $polaczenie = new mysqli($host, $user, $haslo, $baza);
    $polaczenie->set_charset('utf8mb4');
} catch (mysqli_sql_exception $wyjatek) {
    http_response_code(500);
    exit(
        'Nie można połączyć się z bazą danych. Uruchom MySQL w XAMPP i zaimportuj plik database/schema.sql '
        . '(baza baza_filmow).'
    );
}

set_exception_handler(static function (Throwable $wyjatek): void {
    http_response_code(500);
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=UTF-8');
    }
    echo '<!DOCTYPE html><html lang="pl"><meta charset="UTF-8"><title>Błąd aplikacji</title>';
    echo '<p style="font-family:sans-serif">Wystąpił nieoczekiwany błąd aplikacji. Sprawdź, czy baza baza_filmow jest zaimportowana.</p></html>';
    exit;
});
