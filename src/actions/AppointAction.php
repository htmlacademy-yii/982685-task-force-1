<?php
namespace taskforce\actions;

/**
 * AppointAction - Заказчик выбрал исполнителя для задания
 */
class AppointAction extends AbstractAction
{
    protected string $name;
    protected string $internalName;

    /**
     * КОНСТРУКТОР класса
     */
    public function __construct()
    {
        $this->name = 'Назначить';
        $this->internalName = 'appoint';
    }

    /**
     * @inheritDoc
     */
    public static function isAllowed(int $userId, int $customerId, ?int $executorId = null): bool
    {
        return $executorId && ($userId === $customerId);
    }
}
