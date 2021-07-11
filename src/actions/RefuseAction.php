<?php
namespace taskforce\actions;

/**
 * RefuseAction - Исполнитель отказался от выполнения задания
 */
class RefuseAction extends AbstractAction
{
    protected string $name;
    protected string $internalName;

    /**
     * КОНСТРУКТОР класса
     */
    public function __construct()
    {
        $this->name = 'Отказаться';
        $this->internalName = 'refuse';
    }

    /**
     * @inheritDoc
     */
    public static function isAllowed(int $userId, int $customerId, ?int $executorId = null): bool
    {
        return $executorId && ($userId === $executorId);
    }
}
