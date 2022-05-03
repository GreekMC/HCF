<?php

declare(strict_types=1);

namespace juqn\hcf\entity;

use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

/**
 * Class EntityManager
 * @package juqn\hcf\entity
 */
class EntityManager
{
    
    /**
     * EntityManager construct.
     */
    public function __construct()
    {
        EntityFactory::getInstance()->register(TextEntity::class, function (World $world, CompoundTag $nbt): TextEntity {
            return new TextEntity(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['TextEntity', 'minecraft:textentity']);
    }
}