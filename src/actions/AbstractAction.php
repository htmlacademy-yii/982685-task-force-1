<?php
namespace taskforce\actions;

abstract class AbstractAction
{
    protected string $name;
    protected string $internalName;

    /**
     * Возвращает локализованное название действия
     * @return string                   локализованное название действия 
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Возвращает нелокализованное название действия (внутреннее имя)
     * @return string                   название действия
     */
    public function getInternalName(): string
    {
        return $this->internalName;
    }    

    /**
     * Проверяет, доступно ли текущему пользователю выполнение действия
     * @param int $userId               ID текущего пользователя
     * @param int $customerId           ID заказчика задания
     * @param int|null $executorId      ID исполнителя задания
     * @return bool                     True, если пользователь имеет право на действие
     */
    abstract public static function isAllowed(int $userId, int $customerId, ?int $executorId = null): bool;
}
