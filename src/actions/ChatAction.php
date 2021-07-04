<?php
namespace taskforce\actions;

/**
 * ChatAction - Переписка между заказчикои и исполнителем
 */
class ChatAction extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public static function isAllowed(int $userId, int $customerId, ?int $executorId = null): bool
    {
        return ($userId === $customerId || $userId === $executorId);
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'Отправить';
    }

    /**
     * @inheritDoc
     */
    public static function getInternalName(): string
    {
        return 'send';
    }
}
