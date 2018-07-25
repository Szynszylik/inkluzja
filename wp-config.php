<?php
/**
 * Podstawowa konfiguracja WordPressa.
 *
 * Ten plik zawiera konfiguracje: ustawień MySQL-a, prefiksu tabel
 * w bazie danych, tajnych kluczy i ABSPATH. Więcej informacji
 * znajduje się na stronie
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Kodeksu. Ustawienia MySQL-a możesz zdobyć
 * od administratora Twojego serwera.
 *
 * Ten plik jest używany przez skrypt automatycznie tworzący plik
 * wp-config.php podczas instalacji. Nie musisz korzystać z tego
 * skryptu, możesz po prostu skopiować ten plik, nazwać go
 * "wp-config.php" i wprowadzić do niego odpowiednie wartości.
 *
 * @package WordPress
 */

// ** Ustawienia MySQL-a - możesz uzyskać je od administratora Twojego serwera ** //
/** Nazwa bazy danych, której używać ma WordPress */
define('DB_NAME', '');

/** Nazwa użytkownika bazy danych MySQL */
define('DB_USER', '');

/** Hasło użytkownika bazy danych MySQL */
define('DB_PASSWORD', '');

/** Nazwa hosta serwera MySQL */
define('DB_HOST', '');

/** Kodowanie bazy danych używane do stworzenia tabel w bazie danych. */
define('DB_CHARSET', 'utf8');

/** Typ porównań w bazie danych. Nie zmieniaj tego ustawienia, jeśli masz jakieś wątpliwości. */
define('DB_COLLATE', '');

/**#@+
 * Unikatowe klucze uwierzytelniania i sole.
 *
 * Zmień każdy klucz tak, aby był inną, unikatową frazą!
 * Możesz wygenerować klucze przy pomocy {@link https://api.wordpress.org/secret-key/1.1/salt/ serwisu generującego tajne klucze witryny WordPress.org}
 * Klucze te mogą zostać zmienione w dowolnej chwili, aby uczynić nieważnymi wszelkie istniejące ciasteczka. Uczynienie tego zmusi wszystkich użytkowników do ponownego zalogowania się.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'wV-NqYQ$z#Dt0-g|WEI0?+G+XZZ-K?SrS72Qg<Z+O}mBuRs%Q#w4`RJR.m-Ez+<h');
define('SECURE_AUTH_KEY',  'T=8-z1;aiXFuS}^p>q.Nwg*$i?P{u-2{IN,#Qx%;e22V{lziNu#5 !iBk@g(~~|q');
define('LOGGED_IN_KEY',    '`8FN|h+Tai2, Is{7ZPN8C2EOT{yS-&ms3+b]HD6ZVaMC<zaORaZH$a<y9t;U},6');
define('NONCE_KEY',        '}7&M:z_a<[|I~#==OTKaTC]D7tTDB-wTlC[iU<]0GkQ!o;;j/Cxk,W:M~X!E+fM+');
define('AUTH_SALT',        '#G+)M_X}2@{E]O#R[Od3XuPwNjSg6<A<TyiW!;xz.50V^=,>N;@6:]8B#V|$YR9l');
define('SECURE_AUTH_SALT', 'XZYOoCvyS|voN5py5wF,paTVt r:/4LCRSm|~b`fb`50rnhmo+M{V2pL3,*L*-P%');
define('LOGGED_IN_SALT',   'L+l)tnj_nYaV1z+*;.o!h2/?QK-$%2.j|>86l]0;m1`y!+xZ7-SYej$;Sb[pToPi');
define('NONCE_SALT',       '<`/9do+cG)$9I.AY5&iXv+/_I`sy)y,qB(ow5yzPQG+X<&!n-1_+=_a>>E^I&Zn}');


/**#@-*/
/**
 * Prefiks tabel WordPressa w bazie danych.
 *
 * Możesz posiadać kilka instalacji WordPressa w jednej bazie danych,
 * jeżeli nadasz każdej z nich unikalny prefiks.
 * Tylko cyfry, litery i znaki podkreślenia, proszę!
 */
define('WP_CACHE', true);
$table_prefix  = 'wp_';

/**
 * Dla programistów: tryb debugowania WordPressa.
 *
 * Zmień wartość tej stałej na true, aby włączyć wyświetlanie ostrzeżeń
 * podczas modyfikowania kodu WordPressa.
 * Wielce zalecane jest, aby twórcy wtyczek oraz motywów używali
 * WP_DEBUG w miejscach pracy nad nimi.
 */
define('WP_DEBUG', false);

/* To wszystko, zakończ edycję w tym miejscu! Miłego blogowania! */

/** Absolutna ścieżka do katalogu WordPressa. */
if ( !defined('ABSPATH') )
        define('ABSPATH', dirname(__FILE__) . '/');

/** Ustawia zmienne WordPressa i dołączane pliki. */
require_once(ABSPATH . 'wp-settings.php');
