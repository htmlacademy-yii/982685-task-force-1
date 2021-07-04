<?php
namespace taskforce\actions;

/**
 * AppointAction - Заказчик выбрал исполнителя для задания
 */
class AppointAction extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public static function isAllowed(int $userId, int $customerId, ?int $executorId = null): bool
    {
        return $executorId && ($userId === $customerId);
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'Назначить';
    }

    /**
     * @inheritDoc
     */
    public static function getInternalName(): string
    {
        return 'appoint';
    }
}
