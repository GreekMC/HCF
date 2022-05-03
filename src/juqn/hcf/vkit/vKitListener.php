<?php

declare(strict_types=1);

namespace juqn\hcf\vkit;

use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

/**
 * Class vKitListener
 * @package juqn\hcf\vkit
 */
class vKitListener implements Listener
{
 
    /**
     * @param PlayerInteractEvent $event
     */   
    public function handleInteract(PlayerInteractEvent $event): void
    {
        $action = $event->getAction();
        /** @var Player */
        $player = $event->getPlayer();
        
        $item = $player->getInventory()->getItemInHand();
        
        if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            
            if ($item->getNamedTag()->getTag('vkit_name') !== null) {
                $vkit = HCFLoader::getInstance()->getvKitManager()->getvKit($item->getNamedTag()->getString('vkit_name'));
                
                if ($vkit !== null) {
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                    
                    $vkit->openvKit($player);
                }
            }
        }
    }
}