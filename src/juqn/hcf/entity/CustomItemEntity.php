<?php

declare(strict_types=1);

namespace juqn\hcf\entity;

use pocketmine\entity\object\ItemEntity;

class CustomItemEntity extends ItemEntity
{

    protected $gravity = 0.0;
    protected $immobile = true;

    /**
     * @return bool
     */
    public function canBeMovedByCurrents(): bool
    {
        return false;
    }
}