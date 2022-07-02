<?php

declare(strict_types=1);

namespace juqn\hcf\player\disconnected;

use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;
use juqn\hcf\session\Session;
use juqn\hcf\utils\Timer;
use pocketmine\entity\Villager;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class DisconnectedMob extends Villager
{
    
    /** @var Disconnected|null */
    private ?Disconnected $disconnected = null;
    /** @var Player|null */
    private ?Player $lastHit = null;
    
    /** @var int */
    private int $time = 15;
    
    /**
     * @return Disconnected|null
     */
    public function getDisconnected(): ?Disconnected
    {
        return $this->disconnected;
    }
    
    /**
     * @param CompoundTag $nbt
     */
    protected function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt);
        
        if ($this->getDisconnected() === null) {
            $this->flagForDespawn();
            return;
        }
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
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick): bool
    {
        $hasUpdate = parent::onUpdate($currentTick);
        $disconnected = $this->getDisconnected();
        
        if ($hasUpdate) {
            if ($currentTick % 20 === 0) {
                if ($disconnected !== null) {
                    $this->time--;
                    $this->setNameTag(TextFormat::colorize('&7(Combat-Logger)&c ' . $disconnect->getName() . ' &7- &c' . Timer::convert($this->time)));
        
                    if ($this->time <= 0) {
                        HCFLoader::getInstance()->getDisconnectedManager()->removeDisconnected($disconnected->getXuid());
                        $this->flagForDespawn();
                        return true;
                    }
                } else {
                    $this->flagForDespawn();
                    return true;
                }
            }
        }
        return $hasUpdate;
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
                if ($cause !== EntityDamageEvent::CAUSE_ENTITY_ATTACK) {
                    $event->cancel();
                    return;
                }
                
                if ($source instanceof EntityDamageByEntityEvent) {
                    $damager = $source->getDamager();

                    if ($damager instanceof Player) {
                        if ($damager->getName() === $session->getName()) {
                            $event->cancel();
                            return;
                        }
                        
                        if ($damager->getCurrentClaim() === 'Spawn') {
                            $event->cancel();
                            return;
                        }
                    
                        if ($damager->getSession()->getCooldown('starting.timer') !== null || $damager->getSession()->getCooldown('pvp.timer') !== null) {
                            $source->cancel();
                            return;
                        }

                        if ($session->getFaction() !== null && $damager->getSession()->getFaction() !== null) {
                            if ($session->getFaction() === $damager->getSession()->getFaction()) {
                                $source->cancel();
                                return;
                            }
                        }
                        $this->lastHit = $damager;
                        $this->time = 15;
                        
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
        $disconnected = $this->getDisconnected();
        
        if ($disconnected === null)
            return;
        $session = $disconnected->getSession();
        $killerXuid = null;
        $killer = null;
        $itemInHand = null;
        $message = '';
        $damager = $this->lastHit;

        if ($damager instanceof Player) {
            $killerXuid = $damager->getXuid();
            $killer = $damager->getName();
            $itemInHand = $damager->getInventory()->getItemInHand();

            $damager->getSession()->addKill();
            $damager->getSession()->addKillStreak();

            if ($damager->getSession()->getKillStreak() > $damager->getSession()->getHighestKillStreak())
                $damager->getSession()->addHighestKillStreak();

            if ($damager->getSession()->getFaction() !== null) {
                $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($damager->getSession()->getFaction());
                $faction->setPoints($faction->getPoints() + 1);
            }
        }
        $session->setMobKilled(true);
        $session->removeCooldown('spawn.tag');
        $session->addDeath();
        $session->setKillStreak(0);
        $session->addCooldown('pvp.timer', '&l&aPvP Timer&r&7: &r&c', 60 * 60, true);

        if ($session->getFaction() !== null) {
            $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($session->getFaction());
            
            $faction->setPoints($faction->getPoints() - 1);
            $faction->setDtr($faction->getDtr() - 1.0);
            $faction->announce(TextFormat::colorize('&cMember Death: &f' . $player->getName() . PHP_EOL . '&cDTR: &f' . $faction->getDtr()));
            
            # Faction Raid
            if ($faction->getDtr() < 0.00 && !$faction->isRaidable()) {
                $faction->setRaidable(true);
                $faction->setPoints($faction->getPoints() - 10);

                if ($killerXuid !== null) {
                    $session = HCFLoader::getInstance()->getSessionManager()->getSession($killerXuid);

                    if ($session !== null && $session->getFaction()) {
                        $fac = HCFLoader::getInstance()->getFactionManager()->getFaction($session->getFaction());

                        if ($fac !== null) {
                            $fac->setPoints($fac->getPoints() + 3);
                            $fac->announce(TextFormat::colorize('&cThe faction &l' . $faction->getName() . ' &r&cis now Rideable!'));
                        }
                    }
                }
            }
            
            # Regen time
            if (!$faction->isRaidable()) {
               $faction->setTimeRegeneration(35 * 60);
            } else {
                $regenTime = $faction->getTimeRegeneration();
                $value = $regenTime + (5 * 60);

                $faction->setTimeRegeneration($value < 35 * 60 ? $value : 35 * 60);
            }
            
            # Setup scoretag for team members
            foreach ($faction->getOnlineMembers() as $member)
                $member->setScoreTag(TextFormat::colorize('&6[&c' . $faction->getName() . ' &c' . $faction->getDtr() . '■&6]'));
        }

        if ($killer === null) {
            $message = '&c' . $session->getName() . '&4[' . $session->getKills() . '] &edied';
            $webhook = $session->getName() . '[' . $session->getKills() . '] died';
        } else {
            if (!$itemInHand->isNull() && $itemInHand instanceof Tool) {
                $message = '&c' . $session->getName() . '&4[' . $session->getKills() . '] &ewas slain by &c' . $killer . '&4[' . HCFLoader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . '] &cusing ' . $itemInHand->getName();
                $webhook = '`' . $session->getName() . '[' . $session->getKills() . '] was slain by ' . $killer . '[' . HCFLoader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . ']`';
            } else {
                $message = '&c' . $session->getName() . '&4[' . $session->getKills() . '] &ewas slain by &c' . $killer . '&4[' . HCFLoader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . ']';
                $webhook = '`' . $session->getName() . '[' . $session->getKills() . '] was slain by ' . $killer . '[' . HCFLoader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . ']`';
            }
        }
        // Construct a discord webhook with its URL
        $webHook = new Webhook(HCFLoader::getInstance()->getConfig()->get('kills.webhook'));

        // Construct a new Message object
        $msg = new Message();
        $msg->setContent($webhook);
        $webHook->send($msg);
        Server::getInstance()->broadcastMessage(TextFormat::colorize($message));
    }
    
    /**
     * @param Disconnected|null $disconnected
     */
    public function setDisconnected(?Disconnected $disconnected): void
    {
        $this->disconnected = $disconnected;
    }
}
