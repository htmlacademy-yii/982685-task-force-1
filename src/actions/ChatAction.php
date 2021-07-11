<?php
namespace taskforce\actions;

/**
 * ChatAction - Переписка между заказчикои и исполнителем
 */
class ChatAction extends AbstractAction
{
    protected string $name;
    protected string $internalName;

    /**
     * КОНСТРУКТОР класса
     */
    public function __construct()
    {
        $this->name = 'Отправить';
        $this->internalName = 'send';
    }

    /**
     * @inheritDoc
     */
    public static function isAllowed(int $userId, int $customerId, ?int $executorId = null): bool
    {
        return ($userId === $customerId || $userId === $executorId);
    }
}
