<?php
namespace taskforce\model;

use taskforce\actions\{AppointAction, CancelAction, ChatAction, CompleteAction, RefuseAction, RespondAction};

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
     * доступные действия и изменения состояния задания
     */ 
    const STATUSES_MAP = [
        self::STATUS_NEW => 'новое',
        self::STATUS_CANCELLED => 'отменено',
        self::STATUS_PROGRESS => 'в работе',
        self::STATUS_COMPLETED => 'выполнено',
        self::STATUS_FAILED => 'провалено',
    ];
    
    const ACTIONS_MAP = [
        self::ACTION_CANCEL => 'отменить',
        self::ACTION_APPOINT => 'назначить',
        self::ACTION_COMPLETE => 'выполнено',
        self::ACTION_REFUSE => 'отказаться',
        self::ACTION_CHAT  => 'отправить',
    ];

    const NEXT_STATUSES_MAP = [
        self::ACTION_CANCEL => self::STATUS_CANCELLED,
        self::ACTION_APPOINT => self::STATUS_PROGRESS,
        self::ACTION_COMPLETE => self::STATUS_COMPLETED,
        self::ACTION_REFUSE => self::STATUS_FAILED,
    ];

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
     * @param int $customerId               ID заказчика (существует всегда)
     * @param string $completionDate        Срок завершения задания (может отсутствовать)
     */
    public function __construct(int $customerId, ?string $completionDate = null)
    {
        $this->customerId = $customerId;
        $this->executorId = null;           // для нового задания исполнитель отсутствует
        $this->completionDate = $completionDate;
        $this->status = self::STATUS_NEW;
    }

    // МЕТОДЫ класса

    /** 
     * Устанавливает исполнителя для задания
     * @param int $executorId               ID исполнителя
     */
    public function setExecutor(int $executorId): void
    {
        $this->executorId = $executorId;
    }

    /** 
     * Возвращает список всех состояний задания
     * @return array
     */
    public function getStatuses(): array
    {
        return self::STATUSES_MAP;
    }
    
    /**
     * Возвращает список всех действий, которые можно выполнить над заданием
     * @return array
     */
    public function getActions(): array
    {
        return self::ACTIONS_MAP;
    }
    
    /**
     * Возвращает статус, в который перейдёт задача после указанного действия
     * @param string $action                Действие
     * @return string|null
     */
    public function getNextStatus(string $action): ?string
    {
        return self::NEXT_STATUSES_MAP[$action] ?? null;
    }

    /**
     * Возвращает список доступных действий для указанного пользователя и статуса задания
     * @param int $userId                   ID пользователя
     * @param string $status                Статус задания
     * @return array|null                   Список доступных действий или null, если действия отсутствуют
     */
    public function getAvailableActions(int $userId, string $status): ?array
    {
        if (!isset(self::AVAILABLE_ACTIONS_MAP[$status])) {
            return null;
        }

        return array_filter(self::AVAILABLE_ACTIONS_MAP[$status],
            function ($action) use ($userId) {
                return $action::isAllowed($userId, $this->customerId, $this->executorId);
            });
    }
}
