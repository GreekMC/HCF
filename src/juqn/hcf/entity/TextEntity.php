<?php

declare(strict_types=1);

namespace juqn\hcf\entity;

use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

/**
 * Class TextEntity
 * @package juqn\hcf\entity
 */
class TextEntity extends Entity
{
    /**
     * @return string
     */
    public static function getNetworkTypeId() : string{ return EntityIds::BAT; }

    /**
     * @return EntitySizeInfo
     */
    protected function getInitialSizeInfo() : EntitySizeInfo{ return new EntitySizeInfo(0.0, 0.0); }

    /**
     * @param CompoundTag $nbt
     */
    public function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt);
        $this->setScale(0.0001);
        $this->getNetworkProperties()->setFloat(EntityMetadataProperties::BOUNDING_BOX_WIDTH, 0.0);
        $this->getNetworkProperties()->setFloat(EntityMetadataProperties::BOUNDING_BOX_HEIGHT, 0.0);
        $this->setNameTagVisible();
        $this->setNameTagAlwaysVisible();
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void
    {
        $source->cancel();
    }
}