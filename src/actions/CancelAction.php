<?php
namespace taskforce\actions;

/**
 * CancelAction - Заказчик отменил задание
 */
class CancelAction extends AbstractAction
{
    protected string $name;
    protected string $internalName;

    /**
     * КОНСТРУКТОР класса
     */
    public function __construct()
    {
        $this->name = 'Отменить';
        $this->internalName = 'cancel';
    }

    /**
     * @inheritDoc
     */
    public static function isAllowed(int $userId, int $customerId, ?int $executorId = null): bool
    {
        return $userId === $customerId;
    }
}
