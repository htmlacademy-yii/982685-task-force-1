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
use taskforce\exceptions\{TaskActionException, TaskStatusException};

/*
 * подключаем сценарий автозагрузки от Composer
 */
require_once 'vendor/autoload.php';

$customerId = 1;
$executorId = 2;

$actionCancel = new CancelAction;
$actionAppoint = new AppointAction;
$actionComplete = new CompleteAction;
$actionRefuse = new RefuseAction;
$actionRespond = new RespondAction;
$actionChat = new ChatAction;

$task = new Task($customerId);

assert($task->getNextStatus($actionCancel) === Task::STATUS_CANCELLED, 'Отменить --> cancelled');
assert($task->getNextStatus($actionAppoint) === Task::STATUS_PROGRESS, 'Назначить --> progress');
assert($task->getNextStatus($actionComplete) === Task::STATUS_COMPLETED, 'Выполнено --> completed');
assert($task->getNextStatus($actionRefuse) === Task::STATUS_FAILED, 'Отказаться --> failed');
assert(is_null($task->getNextStatus($actionRespond)), 'Откликнуться --> нет изменения статуса задания');
assert(is_null($task->getNextStatus($actionChat)), 'Отправить (сообщение) --> нет изменения статуса задания');

// Доступные действия для заказчика
$userId = $customerId;

assert($task->getActions($userId, Task::STATUS_NEW) === [0 => CancelAction::class], 'STATUS_NEW: заказчик может отменить задание');
assert(in_array([0 => CompleteAction::class], $task->getActions($userId, Task::STATUS_PROGRESS)), 'STATUS_PROGRESS: заказчик может отметить, что задание выполнено');
assert(in_array([2 => ChatAction::class], $task->getActions($userId, Task::STATUS_PROGRESS)), 'STATUS_PROGRESS: заказчик может отправить сообщение исполнителю');
assert(is_null($task->getActions($userId, Task::STATUS_CANCELLED)), 'STATUS_CANCELLED: нет действия ');
assert(is_null($task->getActions($userId, Task::STATUS_COMPLETED)), 'STATUS_COMPLETED: нет действия');
assert(is_null($task->getActions($userId, Task::STATUS_FAILED)), 'STATUS_FAILED: нет действия');

// Доступные действия для исполнителя
$task->setExecutor($executorId);
$userId = $executorId;

assert($task->getActions($userId, Task::STATUS_NEW) === [1 => RespondAction::class], 'STATUS_NEW: исполнитель может откликнуться на задание');
assert(in_array([1 => RefuseAction::class], $task->getActions($userId, Task::STATUS_PROGRESS)), 'STATUS_PROGRESS: исполнитель может отказаться от задания');
assert(in_array([2 => ChatAction::class], $task->getActions($userId, Task::STATUS_PROGRESS)), 'STATUS_PROGRESS: исполнитель может отправить сообщение заказчику');
assert(is_null($task->getActions($userId, Task::STATUS_CANCELLED)), 'STATUS_CANCELLED: нет действия');
assert(is_null($task->getActions($userId, Task::STATUS_COMPLETED)), 'STATUS_COMPLETED: нет действия');
assert(is_null($task->getActions($userId, Task::STATUS_FAILED)), 'STATUS_FAILED: нет действия');

echo 'Все проверки пройдены';

// проверка исключений TaskStatusException
try {
    $task->getActions($customerId, "Unknown_Status");
} catch (TaskStatusException $e) {
    echo "Проверка статуса: " . $e->getMessage();
}

// проверка исключений TaskActionException
try {
    $nextStatus = $task->getNextStatus($actionAppoint);
    echo "Следующий статус: " . $nextStatus;
    $task->setStatus($nextStatus);
} catch (TaskActionException $e) {
    echo "Проверка действия Appoint: " . $e->getMessage();
}

try {
    $nextStatus = $task->getNextStatus($actionRefuse);
    echo "Следующий статус: " . $nextStatus;
    $task->setStatus($nextStatus);
} catch (TaskActionException $e) {
    echo "Проверка действия Refuse: " . $e->getMessage();
}

try {
    $nextStatus = $task->getNextStatus($actionChat);
    echo "Следующий статус: " . $nextStatus;
} catch (TaskActionException $e) {
    echo "Проверка действия Chat: " . $e->getMessage();
}
