<?php
namespace taskforce\actions;

/**
 * RespondAction - Исполнитель откликнулся на задание
 */
class RespondAction extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public static function isAllowed(int $userId, int $customerId, ?int $executorId = null): bool
    {
        return $executorId && ($userId !== $customerId);
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'Откликнуться';
    }

    /**
     * @inheritDoc
     */
    public static function getInternalName(): string
    {
        return 'respond';
    }
}
