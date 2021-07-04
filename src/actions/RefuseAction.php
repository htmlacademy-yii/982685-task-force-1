<?php
namespace taskforce\actions;

/**
 * RefuseAction - Исполнитель отказался от выполнения задания
 */
class RefuseAction extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public static function isAllowed(int $userId, int $customerId, ?int $executorId = null): bool
    {
        return $executorId && ($userId === $executorId);
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'Отказаться';
    }

    /**
     * @inheritDoc
     */
    public static function getInternalName(): string
    {
        return 'refuse';
    }
}
