<?php

declare(strict_types=1);

namespace juqn\hcf\player\disconnect;

use juqn\hcf\player\Player;
use pocketmine\entity\Villager;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class DisconnectedMob extends Villager
{
    
    /** @var Disconnected|null */
    private ?Disconnected $disconnected = null;
    /** @var Player|null */
    private ?Player $lastHit = null;
    
    /**
     * @return Disconnected|null
     */
    public function getDisconnected(): ?Disconnected
    {
        return $this->disconnected;
    }
    
    /**
     * @return Item[]
     */
    public function getDrops(): array
    {
        $drops = [];
        $disconnected = $this->getDisconnected();
        
        if ($disconnected !== null) {
            return array_merge($disconnected->getInventory(), $disconnected->getArmorInventory());
        }
        return $drops;
    }
	
	/**
	 * @return int
     */
    public function getXpDropAmount(): int
    {
        return 0;
    }
    
    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void
    {
        $cause = $source->getCause();
        $disconnected = $this->getDisconnected();
        
        if ($disconnected !== null) {
            $session = $disconnected->getSession();
            
            if ($session !== null) {
                if ($source instanceof EntityDamageByEntityEvent) {
                    $damager = $source->getDamager();

                    if ($damager instanceof Player) {
                        if ($damager->getSession()->getCooldown('starting.timer') !== null || $damager->getSession()->getCooldown('pvp.timer') !== null) {
                            $source->cancel();
                            return;
                        }

                        if ($session->getFaction() !== null && $damager->getSession()->getFaction() !== null) {
                            if ($entity->getSession()->getFaction() === $damager->getSession()->getFaction()) {
                                $source->cancel();
                                return;
                            }
                        }
                        $this->lastHit = $damager;
                        
                        $session->addCooldown('spawn.tag', '&l&cSpawn Tag&r&7: &r&c', 30);
                        $damager->getSession()->addCooldown('spawn.tag', '&l&cSpawn Tag&r&7: &r&c', 30);
                    }
                }
            }
        }
        parent::attack($source);
    }
    
    protected function onDeath(): void
    {
        
        parent::onDeath();
    }
    
    /**
     * @param Disconnected|null $disconnected
     */
    public function setDisconnected(?Disconnected $disconnected): void
    {
        $this->disconnected = $disconnected;
    }
}
