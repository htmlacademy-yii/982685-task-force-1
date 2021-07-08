<?php
namespace taskforce\actions;

/**
 * RespondAction - Исполнитель откликнулся на задание
 */
class RespondAction extends AbstractAction
{
    protected string $name;
    protected string $internalName;

    /**
     * КОНСТРУКТОР класса
     */
    public function __construct()
    {
        $this->name = 'Откликнуться';
        $this->internalName = 'respond';
    }

    /**
     * @inheritDoc
     */
    public static function isAllowed(int $userId, int $customerId, ?int $executorId = null): bool
    {
        return $executorId && ($userId !== $customerId);
    }
}
