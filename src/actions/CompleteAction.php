<?php
namespace taskforce\actions;

/**
 * CompleteAction - Заказчик отметил задание как выполненное
 */
class CompleteAction extends AbstractAction
{
    protected string $name;
    protected string $internalName;

    /**
     * КОНСТРУКТОР класса
     */
    public function __construct()
    {
        $this->name = 'Выполнено';
        $this->internalName = 'complete';
    }

    /**
     * @inheritDoc
     */
    public static function isAllowed(int $userId, int $customerId, ?int $executorId = null): bool
    {
        return $userId === $customerId;
    }
}
