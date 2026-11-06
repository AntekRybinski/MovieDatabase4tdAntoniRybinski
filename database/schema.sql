-- Baza Filmów — schemat i dane testowe (INF.03)
-- Import: phpMyAdmin -> Importuj, albo:
--   C:\xampp\mysql\bin\mysql.exe -u root < database/schema.sql
-- Skrypt można uruchamiać ponownie: najpierw usuwa tabele zależne.

CREATE DATABASE IF NOT EXISTS baza_filmow
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_polish_ci;

USE baza_filmow;

SET NAMES utf8mb4;

DROP TABLE IF EXISTS recenzje;
DROP TABLE IF EXISTS obsada;
DROP TABLE IF EXISTS filmy;
DROP TABLE IF EXISTS aktorzy;

CREATE TABLE filmy (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  tytul VARCHAR(200) NOT NULL,
  rok_produkcji SMALLINT UNSIGNED NOT NULL,
  gatunek VARCHAR(80) NOT NULL,
  opis TEXT NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

CREATE TABLE aktorzy (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  imie VARCHAR(80) NOT NULL,
  nazwisko VARCHAR(100) NOT NULL,
  opis TEXT NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

CREATE TABLE obsada (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  film_id INT UNSIGNED NOT NULL,
  aktor_id INT UNSIGNED NOT NULL,
  rola VARCHAR(150) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_obsada (film_id, aktor_id, rola),
  CONSTRAINT fk_obsada_film
    FOREIGN KEY (film_id) REFERENCES filmy (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_obsada_aktor
    FOREIGN KEY (aktor_id) REFERENCES aktorzy (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

CREATE TABLE recenzje (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  film_id INT UNSIGNED NOT NULL,
  autor VARCHAR(80) NOT NULL,
  ocena INT NOT NULL,
  tresc TEXT NOT NULL,
  data_dodania DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_recenzje_film
    FOREIGN KEY (film_id) REFERENCES filmy (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT chk_ocena CHECK (ocena BETWEEN 1 AND 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

INSERT INTO filmy (id, tytul, rok_produkcji, gatunek, opis) VALUES
(1, 'Incepcja', 2010, 'Science fiction',
 'Złodziej snów Cobb dostaje zlecenie, które może zwrócić mu utracone życie: ma wszczepić obcą myśl w umysł spadkobiercy wielkiej korporacji.'),
(2, 'Skazani na Shawshank', 1994, 'Dramat',
 'Bankier Andy Dufresne trafia do więzienia za zbrodnię, której nie popełnił. Przez lata buduje cichą strategię przetrwania i wolności.'),
(3, 'Ojciec chrzestny', 1972, 'Kryminał',
 'Rodzina Corleone rządzi częścią nowojorskiego podziemia. Najmłodszy syn Michael, dotąd trzymający się z boku, zostaje wciągnięty w sukcesję po ojcu.'),
(4, 'Interstellar', 2014, 'Science fiction',
 'Gdy Ziemi kończą się plony, pilot Cooper wyrusza z grupą naukowców poza Układ Słoneczny, żeby znaleźć planetę zdolną przyjąć ludzkość.'),
(5, 'Pulp Fiction', 1994, 'Kryminał',
 'Splatające się historie gangsterów, boksera i pary drobnych złodziei pokazują Los Angeles od strony barów, zakładów i przypadkowych sojuszy.'),
(6, 'Drużyna Pierścienia', 2001, 'Fantasy',
 'Hobbit Frodo opuszcza Shire z Pierścieniem Władzy. Razem z drużyną rusza ku Górze Przeznaczenia, zanim pierścień wpadnie w ręce wroga.'),
(7, 'Whiplash', 2014, 'Dramat',
 'Młody perkusista Andrew dostaje się pod skrzydła bezwzględnego nauczyciela. Perfekcja staje się ważniejsza niż zdrowie, sen i przyjaźnie.'),
(8, 'Django', 2012, 'Western',
 'Wyzwolony niewolnik i łowca nagród przemierzają amerykańskie Południe w poszukiwaniu żony Django. Na ich drodze staje wpływowy posiadacz plantacji.');

INSERT INTO aktorzy (id, imie, nazwisko, opis) VALUES
(1, 'Leonardo', 'DiCaprio',
 'Amerykański aktor znany z ról wymagających transformacji. W tej bazie gra Cobba oraz Calvina Candie.'),
(2, 'Joseph', 'Gordon-Levitt',
 'Aktor i reżyser, często obsadzany w rolach opanowanych specjalistów. W Incepcji jest prawą ręką Cobba.'),
(3, 'Marion', 'Cotillard',
 'Francuska aktorka, laureatka Oscara. W Incepcji tworzy postać, która prześladuje bohatera w kolejnych warstwach snu.'),
(4, 'Tim', 'Robbins',
 'Aktor i reżyser. Jako Andy Dufresne pokazuje spokojny upór człowieka, który nie zgadza się na wyrok.'),
(5, 'Morgan', 'Freeman',
 'Aktor o charakterystycznym głosie, często grający mentorów i narratorów. W Shawshank jest głosem więziennego podwórka.'),
(6, 'Marlon', 'Brando',
 'Jeden z najważniejszych aktorów kina amerykańskiego. Jego Vito Corleone stał się wzorem filmowego ojca rodziny.'),
(7, 'Al', 'Pacino',
 'Aktor kojarzony z kinem gangsterskim. Michael Corleone to rola o stopniowym wejściu w przemoc i władzę.'),
(8, 'Matthew', 'McConaughey',
 'Aktor z Teksasu, obsadzany w dramatach i kinie science fiction. Cooper łączy zawód pilota z tęsknotą za rodziną.'),
(9, 'Anne', 'Hathaway',
 'Aktorka pracująca w kinie popularnym i dramacie. Doktor Brand jest naukowczynią, która leci w jedną stronę.'),
(10, 'Samuel L.', 'Jackson',
 'Jeden z najczęściej obsadzanych aktorów na świecie. W tej bazie pojawia się jako Jules Winnfield i Stephen.'),
(11, 'John', 'Travolta',
 'Aktor i tancerz. Vincent Vega to chłodny gangster, który między zleceniami rozmawia o drobiazgach codzienności.'),
(12, 'Uma', 'Thurman',
 'Aktorka kojarzona ze współpracą z Quentinem Tarantino. Mia Wallace jest postacią, wokół której kręci się jeden z wieczorów Vincenta.'),
(13, 'Elijah', 'Wood',
 'Aktor, który jako nastolatek zagrał Froda Bagginsa. Jego bohater niesie ciężar, którego nie widać na pierwszy rzut oka.'),
(14, 'Ian', 'McKellen',
 'Brytyjski aktor teatralny i filmowy. Gandalf jest przewodnikiem drużyny i strażnikiem starszej wiedzy.'),
(15, 'Miles', 'Teller',
 'Aktor często grający ludzi ambitnych i niecierpliwych. Andrew Neiman mierzy sukces liczbą uderzeń na minutę.'),
(16, 'J.K.', 'Simmons',
 'Aktor charakterystyczny, laureat Oscara za rolę w Whiplash. Terence Fletcher uczy przez presję, nie przez zachętę.'),
(17, 'Jamie', 'Foxx',
 'Aktor, komik i muzyk. Django jest bohaterem, który od odzyskanej wolności przechodzi do własnej wyprawy.');

INSERT INTO obsada (film_id, aktor_id, rola) VALUES
(1, 1, 'Dom Cobb'),
(1, 2, 'Arthur'),
(1, 3, 'Mal Cobb'),
(2, 4, 'Andy Dufresne'),
(2, 5, 'Ellis Redding'),
(3, 6, 'Vito Corleone'),
(3, 7, 'Michael Corleone'),
(4, 8, 'Joseph Cooper'),
(4, 9, 'Amelia Brand'),
(5, 11, 'Vincent Vega'),
(5, 10, 'Jules Winnfield'),
(5, 12, 'Mia Wallace'),
(6, 13, 'Frodo Baggins'),
(6, 14, 'Gandalf'),
(7, 15, 'Andrew Neiman'),
(7, 16, 'Terence Fletcher'),
(8, 17, 'Django Freeman'),
(8, 1, 'Calvin Candie'),
(8, 10, 'Stephen');

INSERT INTO recenzje (film_id, autor, ocena, tresc, data_dodania) VALUES
(1, 'Anna Kowalska', 9, 'Kolejne warstwy snu są czytelne, a zakończenie zostawia miejsce na własną interpretację. Dobrze trzyma tempo.', '2025-11-02 18:24:00'),
(1, 'Piotr Zieliński', 8, 'Widowisko jest gęste od pomysłów. Przy drugim seansie łatwiej złapać, kto jest w czyim śnie.', '2026-01-14 20:05:00'),
(1, 'Katarzyna Wójcik', 10, 'Najlepszy film o konsekwencjach jednego zawodowego wyboru, jaki widziałam w tym gatunku.', '2026-03-09 21:40:00'),
(2, 'Marek Lis', 10, 'Spokojny, precyzyjny dramat. Przyjaźń Andyego i Reda jest ważniejsza niż sam plan ucieczki.', '2025-08-19 17:12:00'),
(2, 'Ola Nowak', 9, 'Nie spieszy się i właśnie dlatego końcówka działa tak mocno. Warto obejrzeć bez przerywania.', '2026-02-02 19:33:00'),
(3, 'Tomasz Król', 10, 'Klasyk, który nadal dyktuje tempo kinu gangsterskiego. Sceny rodzinne są tu równie ważne jak przestępstwa.', '2025-06-11 16:48:00'),
(3, 'Julia Adamska', 9, 'Michael zmienia się scena po scenie. To właśnie ta przemiana zostaje na dłużej niż cytaty.', '2025-12-28 22:15:00'),
(3, 'Bartek Sowa', 8, 'Długi, ale konsekwentny. Trzeci akt pokazuje cenę lojalności wobec rodziny.', '2026-04-18 18:02:00'),
(4, 'Natalia Maj', 9, 'Skala kosmosu nie przykrywa historii ojca i córki. Efekty służą emocjom, a nie odwrotnie.', '2025-09-30 20:20:00'),
(4, 'Wojciech Dudek', 10, 'Zegar na statku i zegar na Ziemi robią z science fiction dramat o czasie, którego nie da się dogonić.', '2026-05-07 21:11:00'),
(5, 'Helena Bąk', 8, 'Dialogi niosą film mocniej niż intryga. Kolejność scen przestaje przeszkadzać po pierwszym kwadransie.', '2025-07-22 23:05:00'),
(5, 'Igor Pawlak', 9, 'Trzy historie, jeden świat. Scena w restauracji jest zabawna i jednocześnie bardzo napięta.', '2026-01-29 19:44:00'),
(5, 'Zofia Kaczor', 7, 'Styl jest wyrazisty, choć nie każda dygresja jest równie potrzebna. I tak wracam do wybranych fragmentów.', '2026-06-13 15:27:00'),
(6, 'Maja Górska', 9, 'Początek wielkiej wyprawy nakręcony z szacunkiem do krajobrazu i do małych bohaterów.', '2025-10-16 18:55:00'),
(6, 'Filip Czajka', 10, 'Drużyna jest tu ważniejsza niż pojedyncze pojedynki. Dobrze oddaje ciężar wspólnej drogi.', '2026-02-27 20:36:00'),
(7, 'Alicja Sikora', 9, 'Sala prób jest tu bardziej stresująca niż niejeden thriller. Gra obu aktorów trzyma w napięciu do końca.', '2025-05-03 17:41:00'),
(7, 'Damian Wróbel', 8, 'Film pyta, ile wolno poświęcić dla wyniku. Nie daje wygodnej odpowiedzi.', '2026-03-21 22:08:00'),
(7, 'Ewa Polak', 10, 'Krótki, ostry i precyzyjny. Perkusja działa jak metronom całej historii.', '2026-08-01 19:19:00'),
(8, 'Robert Milewski', 8, 'Western o wolności i zemście, z bardzo wyraźnym przeciwnikiem. DiCaprio gra tu przeciw swoim zwykłym bohaterom.', '2025-11-25 21:52:00'),
(8, 'Sara Jankowska', 7, 'Środek filmu jest mocny, zakończenie bardziej umowne. Warto zobaczyć ze względu na obsadę.', '2026-07-14 18:46:00');

ALTER TABLE filmy AUTO_INCREMENT = 9;
ALTER TABLE aktorzy AUTO_INCREMENT = 18;
ALTER TABLE obsada AUTO_INCREMENT = 20;
ALTER TABLE recenzje AUTO_INCREMENT = 21;
