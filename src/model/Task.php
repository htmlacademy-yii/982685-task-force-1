<?php
namespace taskforce\model;

use taskforce\actions\{AbstractAction, AppointAction, CancelAction, ChatAction, CompleteAction, RefuseAction, RespondAction};
use taskforce\exceptions\{TaskStatusException, TaskActionException};

class Task
{
    /**
     * список всех состояний (статусов) заданий и действий
     */
    const STATUS_NEW = 'new';               // Новое     - задание опубликовано, исполнитель ещё не найден
    const STATUS_CANCELLED = 'cancelled';   // Отменено  - заказчик отменил задание
    const STATUS_PROGRESS = 'progress';     // В работе  - заказчик выбрал исполнителя для задания
    const STATUS_COMPLETED = 'completed';   // Выполнено - заказчик отметил задание как выполненное
    const STATUS_FAILED = 'failed';         // Провалено - исполнитель отказался от выполнения задания

    const ACTION_CANCEL = CancelAction::class;
    const ACTION_RESPOND = RespondAction::class;
    const ACTION_APPOINT = AppointAction::class;
    const ACTION_COMPLETE = CompleteAction::class;
    const ACTION_REFUSE = RefuseAction::class;
    const ACTION_CHAT = ChatAction::class;

    /**
     * изменения состояния задания
     */
    const NEXT_STATUSES_MAP = [
        'cancel' => self::STATUS_CANCELLED,
        'appoint' => self::STATUS_PROGRESS,
        'complete' => self::STATUS_COMPLETED,
        'refuse' => self::STATUS_FAILED,
    ];

    /**
     * доступные действия в зависимости от состояния задания
     */
    const AVAILABLE_ACTIONS_MAP = [
        self::STATUS_NEW => [               // НОВОЕ ЗАДАНИЕ:
            self::ACTION_CANCEL,            // Заказчик может отменить задание
            self::ACTION_RESPOND,           // Исполнитель может откликнуться на задание
            self::ACTION_APPOINT,           // Заказчик может выбрать исполнителя для задания
        ],
        self::STATUS_PROGRESS => [          // ЗАДАНИЕ В РАБОТЕ:
            self::ACTION_COMPLETE,          // Заказчик может отметить задание как выполненное
            self::ACTION_REFUSE,            // Исполнитель может отказаться от выполнения задания
            self::ACTION_CHAT,              // Заказчик и исполнитель могут отправлять сообщения друг другу
        ],
    ];

    /**
     * список всех возможных ролей пользователей
     */
    const ROLE_CUSTOMER = 'customer';       // Заказчик
    const ROLE_EXECUTOR = 'executor';       // Исполнитель

    /**
     * СВОЙСТВА класса
     */
    private $customerId;                    // ID заказчика
    private $executorId;                    // ID исполнителя
    private $completionDate;                // Срок завершения задания
    private $status;                        // Текущий статус задания

    /**
     * КОНСТРУКТОР класса
     * @param int $customerId               ID заказчика
     * @param int $executorId               ID исполнителя
     * @param string $completionDate        Срок завершения задания (может отсутствовать)
     */
    public function __construct(int $customerId, ?int $executorId = null, ?string $completionDate = null)
    {
        $this->customerId = $customerId;
        $this->executorId = $executorId;    // для нового задания исполнитель отсутствует
        $this->completionDate = $completionDate;
        $this->status = self::STATUS_NEW;
    }

    // МЕТОДЫ класса

    /**
     * Возвращает список статусов
     * @return array
     */
    public function getStatuses(): array
    {
        return [self::STATUS_NEW, self::STATUS_CANCELLED, self::STATUS_PROGRESS, self::STATUS_COMPLETED, self::STATUS_FAILED];
    }

    /**
     * Возвращает статус, в который перейдёт задание после указанного действия
     * @param AbstractAction $action        Действие
     * @return string
     * @throws TaskActionException          Исключение, если передано неверное действие
     */
    public function getNextStatus(AbstractAction $action): ?string
    {
        if (!array_key_exists($action->getInternalName(), self::NEXT_STATUSES_MAP)) {
            throw new TaskActionException("Передано неверное действие");
        }

        return self::NEXT_STATUSES_MAP[$action->getInternalName()];
    }

    /**
     * Возвращает список доступных действий для указанного пользователя и статуса задания
     * @param int $userId                   ID пользователя
     * @param string $status                Статус задания
     * @return array|null                   Список доступных действий или null, если действия отсутствуют
     * @throws TaskStatusException          Исключение, если будет передан несуществующий статус
     */
    public function getActions(int $userId, string $status): ?array
    {

        if (!in_array($status, $this->getStatuses())) {
            throw new TaskStatusException("Передан неизвестный статус задания");
        }

        if (!isset(self::AVAILABLE_ACTIONS_MAP[$status])) {
            return null;
        }

        return array_filter(
            self::AVAILABLE_ACTIONS_MAP[$status],
            function ($action) use ($userId) {
                return $action::isAllowed($userId, $this->customerId, $this->executorId);
            }
        );
    }

    /** 
     * Устанавливает исполнителя для задания
     * @param int $executorId               ID исполнителя
     */
    public function setExecutor(int $executorId): void
    {
        $this->executorId = $executorId;
    }

    /** 
     * Устанавливает статус для задания
     * @param string $status                Новый статус задания
     */
    public function setStatus(string $status): void
    {
        if (!in_array($status, $this->getStatuses())) {
            throw new TaskStatusException("Передан неизвестный статус задания");
        }

        $this->status = $status;
    }
}
