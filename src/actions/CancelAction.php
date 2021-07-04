<?php
namespace taskforce\actions;

/**
 * CancelAction - Заказчик отменил задание
 */
class CancelAction extends AbstractAction
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
        return 'Отменить';
    }

    /**
     * @inheritDoc
     */
    public static function getInternalName(): string
    {
        return 'cancel';
    }
}
