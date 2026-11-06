document.addEventListener('DOMContentLoaded', function () {
    var formularz = document.getElementById('form-recenzja');
    if (!formularz) {
        return;
    }

    var komunikat = document.getElementById('blad-formularza');

    formularz.addEventListener('submit', function (zdarzenie) {
        var autor = formularz.autor.value.trim();
        var tresc = formularz.tresc.value.trim();
        var surowaOcena = formularz.ocena.value.trim();
        var ocena = Number(surowaOcena);
        var tekst = '';

        if (autor === '' || tresc === '' || surowaOcena === '') {
            tekst = 'Uzupełnij autora, ocenę i treść recenzji.';
        } else if (!/^\d+$/.test(surowaOcena) || ocena < 1 || ocena > 10) {
            tekst = 'Ocena musi być liczbą całkowitą od 1 do 10.';
        }

        if (tekst !== '') {
            zdarzenie.preventDefault();
            komunikat.textContent = tekst;
            komunikat.hidden = false;
            return;
        }

        komunikat.hidden = true;
    });
});
