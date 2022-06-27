<?php

declare(strict_types=1);

namespace juqn\hcf\event;

use juqn\hcf\HCFLoader;
use juqn\hcf\player\disconnected\DisconnectedMob;
use juqn\hcf\player\Player;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;

/**
 * Class EventListener
 * @package juqn\hcf\event
 */
class EventListener implements Listener
{
    
    /**
     * @param EntityDamageEvent $event
     */
    public function handleDamage(EntityDamageEvent $event): void
    {
        $entity = $event->getEntity();
        
        if ($event->isCancelled()) return;
        
        if ($entity instanceof Player || $entity instanceof DisconnectedMob) {
            if (HCFLoader::getInstance()->getEventManager()->getSotw()->isActive())
                $event->cancel();
        }
    }
}