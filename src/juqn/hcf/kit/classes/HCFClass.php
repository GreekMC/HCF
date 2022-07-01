<?php

declare(strict_types=1);

namespace juqn\hcf\kit\classes;

use juqn\hcf\player\Player;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;

abstract class HCFClass
{
    
    /** @var int */
    public const ARCHER = 0;
    /** @var int */
    public const BARD = 1;
    /** @var int */
    public const MAGE = 2;
    /** @var int */
    public const MINER = 3;
    /** @var int */
    public const ROGUE = 4;

    /** @var int */
    private int $id;

    /**
     * HCFClass construct.
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return Item[]
     */
    abstract public function getArmorItems(): array;

    /**
     * @return EffectInstance[]
     */
    abstract public function getEffects(): array;
    
    /**
     * @param Player $player
     * @return bool
     */
    public function isActive(Player $player): bool
    {
        $inventory = $player->getArmorInventory();
        $items = $this->getArmorItems();
        
        if ($inventory->getHelmet()->getId() === $items[0]->getId() and
            $inventory->getChestplate()->getId() === $items[1]->getId() and
            $inventory->getLeggings()->getId() === $items[2]->getId() and
            $inventory->getBoots()->getId() === $items[3]->getId())
            return true;
        return false;
    }
    
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * @param EntityDamageEvent $event
     */
    public function handleDamage(EntityDamageEvent $event): void
    {
    }
    
    /**
     * @param EntityDamageByChildEntityEvent $event
     */
    public function handleDamageByChildEntity(EntityDamageByChildEntityEvent $event): void
    {
    }
    
    /**
     * @param PlayerItemHeldEvent $event
     */
    public function handleItemHeld(PlayerItemHeldEvent $event): void
    {
    }
    
    /**
     * @param PlayerItemUseEvent $event
     */
    public function handleItemUse(PlayerItemUseEvent $event): void
    {
    }
    
    /**
     * @param Player $player
     */
    public function onRun(Player $player): void
    {
        if (!$this->isActive($player)) {
            $player->setClass(null);
            
            if ($this->getId() === self::BARD) {
                if ($player->getSession()->getEnergy('bard.energy') !== null)
                    $player->getSession()->removeEnergy('bard.energy');
            }
            return;
        }
        
        foreach($this->getEffects() as $effect)
            $player->getEffects()->add($effect);

        if ($this->getId() === self::BARD) {
            if ($player->getSession()->getEnergy('bard.energy') === null ) {
                $player->getSession()->addEnergy('bard.energy', '&l&9Bard Energy&r&7: &r&c');
            }
            
            if ($player->getSession()->getEnergy('bard.energy')->getEnergy() >= 120 ) {
                // $player->getSession()->getEnergy('bard.energy')->setPaused(true);
                return;
            }
            $player->getSession()->getEnergy('bard.energy')->update();
        }
    }
}
