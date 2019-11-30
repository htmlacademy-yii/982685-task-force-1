<?php
/*
 * В начале сценария надо перечислить все используемые пространства имён
 */
use taskforce\model\Task;

/*
 * Подключаем сценарий автозагрузки от Composer
 */
require_once 'vendor/autoload.php';

/*
 * Вызов класса по его имени вызывает механизм автозагрузки
 */
$task = new Task(1, null, '2019-12-01');

/*
 * Вызов метода объекта, чтобы продемонстрировать успешную загрузку
 */
$аvailable_actions = $task -> getActions();
var_dump($аvailable_actions);
