<?php

declare(strict_types=1);

namespace juqn\hcf\entity;

use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\data\bedrock\EntityLegacyIds as LegacyIds;
use pocketmine\data\bedrock\PotionTypeIdMap;
use pocketmine\data\bedrock\PotionTypeIds;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityDataHelper as Helper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\object\ItemEntity;
use pocketmine\item\Item;
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
        EntityFactory::getInstance()->register(CustomItemEntity::class, function(World $world, CompoundTag $nbt) : ItemEntity{
            $itemTag = $nbt->getCompoundTag("Item");
            
            if ($itemTag === null) {
                throw new SavedDataLoadingException("Expected \"Item\" NBT tag not found");
            }
            $item = Item::nbtDeserialize($itemTag);
            
            if ($item->isNull()) {
                throw new SavedDataLoadingException("Item is invalid");
            }
            return new CustomItemEntity(Helper::parseLocation($nbt, $world), $item, $nbt);
        }, ['Item', 'minecraft:item'], LegacyIds::ITEM);

        EntityFactory::getInstance()->register(TextEntity::class, function (World $world, CompoundTag $nbt): TextEntity {
            return new TextEntity(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['TextEntity', 'minecraft:textentity']);

        EntityFactory::getInstance()->register(EnderPearlEntity::class, function (World $world, CompoundTag $nbt): EnderPearlEntity {
            return new EnderPearlEntity(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['ThrownEnderpearl', 'minecraft:ender_pearl'], EntityLegacyIds::ENDER_PEARL);

        EntityFactory::getInstance()->register(TopKillsEntity::class, function (World $world, CompoundTag $nbt): TopKillsEntity {
            return new TopKillsEntity(EntityDataHelper::parseLocation($nbt, $world), TopKillsEntity::parseSkinNBT($nbt), $nbt);
        }, ['TopKillsEntity']);

        EntityFactory::getInstance()->register(TopKDREntity::class, function (World $world, CompoundTag $nbt): TopKDREntity {
            return new TopKDREntity(EntityDataHelper::parseLocation($nbt, $world), TopKDREntity::parseSkinNBT($nbt), $nbt);
        }, ['TopKDREntity']);

        EntityFactory::getInstance()->register(TopFactionsEntity::class, function (World $world, CompoundTag $nbt): TopFactionsEntity {
            return new TopFactionsEntity(EntityDataHelper::parseLocation($nbt, $world), TopFactionsEntity::parseSkinNBT($nbt), $nbt);
        }, ['TopFactionsEntity']);

        EntityFactory::getInstance()->register(SplashPotionEntity::class, function(World $world, CompoundTag $nbt): SplashPotionEntity {
            $potionType = PotionTypeIdMap::getInstance()->fromId($nbt->getShort('PotionId', PotionTypeIds::WATER));
            
            if ($potionType === null) {
                throw new SavedDataLoadingException('No such potion type');
            }
            return new SplashPotionEntity(EntityDataHelper::parseLocation($nbt, $world), null, $potionType, $nbt);
        }, ['ThrownPotion', 'minecraft:potion', 'thrownpotion'], EntityLegacyIds::SPLASH_POTION);
    }
}