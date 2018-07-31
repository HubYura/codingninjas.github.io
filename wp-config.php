<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'wisesolu_codingn');

/** Имя пользователя MySQL */
define('DB_USER', 'wisesolu_codingn');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', '8edfa279');

/** Имя сервера MySQL */
define('DB_HOST', 'wisesolu.mysql.tools');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8mb4');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'pWFFpiZ:3f`t3}?./u`U8td^^i&Y&)~G4t/I1)CWM$7B]bp~Bb_ID?x1$60d?[#)');
define('SECURE_AUTH_KEY',  'a2&M.?T#O@+N5?WHAmu7&2/2J{#wO~S$r&aWpPN+V`{%Jgc48y|,~5?wU)@jp5`)');
define('LOGGED_IN_KEY',    '<1e1M9=+B#%U9>,yjc(WR8a-:|^`(=N$5:A{M.!/yS0Nm:Y5T?Ad En~trS 437S');
define('NONCE_KEY',        'djN<AQ}nvCP^RT;W.3kEhkUI&j!j;#1 f%JT^/otuHNl!D[:6U,Wr2t+eRAc~j}x');
define('AUTH_SALT',        '7Ky(g]R~PkeOppfT9>*M**kN8T~$Q0BS(+ QmOs<NB }n>;6`#`e`r>!trs/`~af');
define('SECURE_AUTH_SALT', '!D,}1):C~{Z!PG:My>Rum.+S[48B#Y&2w$-9x6ua;R#T99A|gsfh:lN)5L:_$:rW');
define('LOGGED_IN_SALT',   '3~BNr9](q#/-2OQj~L0H+Y}HQh|c8B(y<hT).j>]^^|R?TPzvShL/rpr/7$LuFxv');
define('NONCE_SALT',       '6vQJ[3r:3d )Hq,sBXN|km_V[19])J<GL}nDU2U!.V-V`bc=3)l`_f`A4$64n!mq');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', true);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
