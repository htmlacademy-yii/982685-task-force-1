<?php
/**
 * устанавливаем значения настроек конфигурации
 */
ini_set('error_reporting', E_ALL);      // уровень протоколирования ошибок: все
ini_set('display_errors', 1);           // требуется ли выводить ошибки на экран вместе с остальным выводом

/*
 * устанавливаем значения настроек механизма проверки утверждений
 */
assert_options(ASSERT_ACTIVE, 1);       // включение механизма проверки утверждений
assert_options(ASSERT_WARNING, 1);      // вывод предупреждения PHP для каждой неудачной проверки

/*
 * перечисляем все используемые пространства имён
 */
use taskforce\utils\{CategoriesCsvConverter, CitiesCsvConverter, ReviewsCsvConverter, TasksCsvConverter, UsersCsvConverter};

/*
 * подключаем сценарий автозагрузки от Composer
 */
require_once 'vendor/autoload.php';

/*
 * конвертируем записи из датасетов в формат SQL
 */

// справочник городов
$src = 'data/cities.csv';
print "Конвертируем файл " . $src . "... ";
$cities = new CitiesCsvConverter($src, ['city', 'lat', 'long']);
$cities->сonverter();
print "успешно. ";

// категории услуг
$src = 'data/categories.csv';
print "Конвертируем файл " . $src . "... ";
$categories = new CategoriesCsvConverter($src, ['name', 'icon']);
$categories->сonverter();
print "успешно. ";

// пользователи + профили
$src = 'data/users.csv';
print "Конвертируем файл " . $src . "... ";
$users = new UsersCsvConverter($src, ['email', 'name', 'password', 'dt_add', 'address', 'bd', 'about', 'phone', 'skype']);
$users->сonverter();
print "успешно. ";

// задачи
$src = 'data/tasks.csv';
print "Конвертируем файл " . $src . "... ";
$tasks = new TasksCsvConverter($src, ['dt_add', 'category_id', 'description', 'expire', 'name', 'address', 'budget', 'lat', 'long']);
$tasks->сonverter();
print "успешно. ";

// отзывы
$src = 'data/replies.csv';
print "Конвертируем файл " . $src . "... ";
$reviews = new ReviewsCsvConverter($src, ['dt_add', 'rate', 'description']);
$reviews->сonverter();
print "успешно. ";

print "Порядок импорта: ";
print "1. cities.sql; ";
print "2. categories.sql; ";
print "3. users.sql; ";
print "4. tasks.sql; ";
print "5. replies.sql.";
?>
