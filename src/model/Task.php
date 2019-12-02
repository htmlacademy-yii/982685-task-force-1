<?php
namespace taskforce\model;

class Task
{
    /**
     * список всех доступных статусов задач
     */
    const STATUS_NEW = 'new';               // Новое - задание опубликовано, исполнитель ещё не найден
    const STATUS_CANCELLED = 'cancelled';   // Отменено - заказчик отменил задание
    const STATUS_PROGRESS = 'progress';     // В работе - заказчик выбрал исполнителя для задания
    const STATUS_COMPLETED = 'completed';   // Выполнено - заказчик отметил задание как выполненное
    const STATUS_FAILED = 'failed';         // Провалено - исполнитель отказался от выполнения задания

    /**
     * список всех возможных ролей пользователей
     */
    const ROLE_CUSTOMER = 'customer';       // Заказчик
    const ROLE_EXECUTOR = 'executor';       // Исполнитель

    /**
     * список всех доступных действий над задачами
     */
    // Заказчик
    const ACTION_CANCEL = 'cancel';         // Отменить
    const ACTION_APPOINT = 'appoint';       // Назначить
    const ACTION_COMPLETE = 'complete';     // Завершить
    // Исполнитель
    const ACTION_RESPOND = 'respond';       // Откликнуться
    const ACTION_REFUSE = 'refuse';         // Отказаться

    /**
     * СВОЙСТВА класса
     */
    private $id_customer = null;            // id заказчика
    private $id_executor = null;            // id исполнителя
    private $completion_date = null;        // срок завершения задачи
    private $current_status = null;         // активный статус

    /**
     * КОНСТРУКТОР класса
     * @param int $id_customer ID заказчика
     * @param int $id_executor ID исполнителя
     * @param string $completion_date Срок завершения задачи
     * @param string $status Статус задачи
     */
    public function __construct(int $id_customer, ?int $id_executor = null, ?string $completion_date = null, ?string $status = self::STATUS_NEW)
    {
        $this->id_customer = $id_customer;
        $this->id_executor = $id_executor;
        $this->completion_date = $completion_date;
        $this->current_status = $status;
    }

    // МЕТОДЫ класса

    /**
     * Возвращает список действий
     * @return array
     */
    public function getActions(): array
    {
        return [self::ACTION_CANCEL, self::ACTION_APPOINT, self::ACTION_COMPLETE, self::ACTION_RESPOND, self::ACTION_REFUSE];
    }

    /** Возвращает список статусов
     * @return array
     */
    public function getStatuses(): array
    {
        return [self::STATUS_NEW, self::STATUS_CANCELLED, self::STATUS_PROGRESS, self::STATUS_COMPLETED, self::STATUS_FAILED];
    }

    /** Возвращает статус, в который перейдет задача для указанного действия
     * @param string $action Действие
     * @return string|null Статус задачи
     */
    public function getNextStatus(string $action): string
    {
        // доступные действия и изменение состояния задачи
        $links = [
            self::ACTION_CANCEL => STATUS_CANCELLED,
            self::ACTION_APPOINT => STATUS_PROGRESS,
            self::ACTION_COMPLETE => STATUS_COMPLETED,
            self::ACTION_REFUSE => STATUS_FAILED,
            self::ACTION_RESPOND => null
        ];

        if (array_key_exists($action, $links)) {
            return $links[$action];
        }

        return null;
    }
}
