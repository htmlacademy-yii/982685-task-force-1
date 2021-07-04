<?php
namespace taskforce\actions;

/**
 * CompleteAction - Заказчик отметил задание как выполненное
 */
class CompleteAction extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public static function isAllowed(int $userId, int $customerId, ?int $executorId = null): bool
    {
        return $userId === $customerId;
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'Выполнено';
    }

    /**
     * @inheritDoc
     */
    public static function getInternalName(): string
    {
        return 'complete';
    }
}
