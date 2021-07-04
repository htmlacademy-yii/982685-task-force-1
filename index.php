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
use taskforce\model\Task;
use taskforce\actions\{AppointAction, CancelAction, ChatAction, CompleteAction, RefuseAction, RespondAction};

/*
 * подключаем сценарий автозагрузки от Composer
 */
require_once 'vendor/autoload.php';

$customerId = 1;
$executorId = 2;

$task = new Task($customerId);

assert($task->getStatuses() === Task::STATUSES_MAP, 'Список статусов заданий');
assert($task->getActions() === Task::ACTIONS_MAP, 'Список действий');

assert($task->getNextStatus(Task::ACTION_CANCEL) === Task::STATUS_CANCELLED, 'Отменить --> cancelled');
assert($task->getNextStatus(Task::ACTION_APPOINT) === Task::STATUS_PROGRESS, 'Назначить --> progress');
assert($task->getNextStatus(Task::ACTION_COMPLETE) === Task::STATUS_COMPLETED, 'Выполнено --> completed');
assert($task->getNextStatus(Task::ACTION_REFUSE) === Task::STATUS_FAILED, 'Отказаться --> failed');
assert(is_null($task->getNextStatus(Task::ACTION_RESPOND)), 'Откликнуться --> нет изменения статуса задания');
assert(is_null($task->getNextStatus(Task::ACTION_CHAT)), 'Отправить (сообщение) --> нет изменения статуса задания');

// Доступные действия для заказчика
$userId = $customerId;

assert($task->getAvailableActions($userId, Task::STATUS_NEW) === [0 => CancelAction::class], 'STATUS_NEW: заказчик может отменить задание');
assert(in_array([0 => CompleteAction::class], $task->getAvailableActions($userId, Task::STATUS_PROGRESS)), 'STATUS_PROGRESS: заказчик может отметить, что задание выполнено');
assert(in_array([2 => ChatAction::class], $task->getAvailableActions($userId, Task::STATUS_PROGRESS)), 'STATUS_PROGRESS: заказчик может отправить сообщение исполнителю');
assert(is_null($task->getAvailableActions($userId, Task::STATUS_CANCELLED)), 'STATUS_CANCELLED: нет действия ');
assert(is_null($task->getAvailableActions($userId, Task::STATUS_COMPLETED)), 'STATUS_COMPLETED: нет действия');
assert(is_null($task->getAvailableActions($userId, Task::STATUS_FAILED)), 'STATUS_FAILED: нет действия');

// Доступные действия для исполнителя
$task->setExecutor($executorId);
$userId = $executorId;

assert($task->getAvailableActions($userId, Task::STATUS_NEW) === [1 => RespondAction::class], 'STATUS_NEW: исполнитель может откликнуться на задание');
assert(in_array([1 => RefuseAction::class], $task->getAvailableActions($userId, Task::STATUS_PROGRESS)), 'STATUS_PROGRESS: исполнитель может отказаться от задания');
assert(in_array([2 => ChatAction::class], $task->getAvailableActions($userId, Task::STATUS_PROGRESS)), 'STATUS_PROGRESS: исполнитель может отправить сообщение заказчику');
assert(is_null($task->getAvailableActions($userId, Task::STATUS_CANCELLED)), 'STATUS_CANCELLED: нет действия');
assert(is_null($task->getAvailableActions($userId, Task::STATUS_COMPLETED)), 'STATUS_COMPLETED: нет действия');
assert(is_null($task->getAvailableActions($userId, Task::STATUS_FAILED)), 'STATUS_FAILED: нет действия');

echo 'Все проверки пройдены';
