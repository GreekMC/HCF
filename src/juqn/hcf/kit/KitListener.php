<?php

declare(strict_types=1);

namespace juqn\hcf\kit;

use juqn\hcf\HCFLoader;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerItemUseEvent;

class KitListener implements Listener
{
    
    /**
     * @param EntityDamageEvent $event
     */
    public function handleDamage(EntityDamageEvent $event): void
    {
        if ($event->isCancelled())
            return;
        HCFLoader::getInstance()->getKitManager()->callEvent(__FUNCTION__, $event);
    }
    
    /**
     * @param EntityDamageByChildEntityEvent $event
     */
    public function handleDamageByChildEntity(EntityDamageByChildEntityEvent $event): void
    {
        if ($event->isCancelled())
            return;
        HCFLoader::getInstance()->getKitManager()->callEvent(__FUNCTION__, $event);
    }
    
    /**
     * @param PlayerItemHeldEvent $event
     */
    public function handleItemHeld(PlayerItemHeldEvent $event): void
    {
        if ($event->isCancelled())
            return;
        HCFLoader::getInstance()->getKitManager()->callEvent(__FUNCTION__, $event);
    }
    
    /**
     * @param PlayerItemUseEvent $event
     */
    public function handleItemUse(PlayerItemUseEvent $event): void
    {
        if ($event->isCancelled())
            return;
        HCFLoader::getInstance()->getKitManager()->callEvent(__FUNCTION__, $event);
    }
}