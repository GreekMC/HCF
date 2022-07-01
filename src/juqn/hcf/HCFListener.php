<?php

declare(strict_types=1);

namespace juqn\hcf;

use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use juqn\hcf\player\Player;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Armor;
use pocketmine\item\Tool;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;

class HCFListener implements Listener
{
    
    /**
     * @param EntityDamageEvent $event
     * @priority HIGH
     */
    public function handleDamage(EntityDamageEvent $event): void
    {
        $cause = $event->getCause();
        $entity = $event->getEntity();

        if ($entity instanceof Player) {
            if ($event->isCancelled()) return;

            if ($entity->getSession()->getCooldown('starting.timer')) {
                $event->cancel();
                return;
            }

            if ($entity->getSession()->getCooldown('pvp.timer') !== null) {
                if ($cause === EntityDamageEvent::CAUSE_ENTITY_ATTACK || $cause === EntityDamageEvent::CAUSE_PROJECTILE) {
                    $event->cancel();
                    return;
                }
            }

            if ($entity->getCurrentClaim() === 'Spawn') {
                $event->cancel();
                return;
            }

            if ($event instanceof EntityDamageByEntityEvent || $event instanceof EntityDamageByChildEntityEvent) {
                $damager = $event->getDamager();

                if ($damager instanceof Player) {
                    if ($damager->getSession()->getCooldown('starting.timer') !== null || $damager->getSession()->getCooldown('pvp.timer') !== null) {
                        $event->cancel();
                        return;
                    }
                    
                    if ($damager->getCurrentClaim() === 'Spawn') {
                        $event->cancel();
                        return;
                    }

                    if ($entity->getSession()->getFaction() !== null && $damager->getSession()->getFaction() !== null) {
                        if ($entity->getSession()->getFaction() === $damager->getSession()->getFaction()) {
                            $damager->sendMessage(TextFormat::colorize('&eYou cannot hurt &2' . $entity->getName() . '§e.'));
                            $event->cancel();
                            return;
                        }
                    }
                    $entity->getSession()->addCooldown('spawn.tag', '&l&cSpawn Tag&r&7: &r&c', 30);
                    $damager->getSession()->addCooldown('spawn.tag', '&l&cSpawn Tag&r&7: &r&c', 30);
                }
            }
        }
    }
    
    /**
     * @param PlayerChatEvent $event
     */
    public function handleChat(PlayerChatEvent $event): void
    {
        $player = $event->getPlayer();
        $message = $event->getMessage();

        if ($player instanceof Player) {
            if ($player->getSession()->getFaction() !== null && $player->getSession()->hasFactionChat()) {
                $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($player->getSession()->getFaction());
                
                if ($faction !== null) {
                    $event->cancel();
                    
                    foreach ($faction->getOnlineMembers() as $member)
                        $member->sendMessage(TextFormat::colorize('&9(Team) ' . $player->getName() . ': §e' . $message));
                    return;
                }
            }
        }
    }

    /**
     * @param PlayerCreationEvent $event
     */
    public function handleCreation(PlayerCreationEvent $event): void
    {
        $event->setPlayerClass(Player::class);
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function handleDeath(PlayerDeathEvent $event): void
    {
        /** @var Player */
        $player = $event->getPlayer();

        if (!$player instanceof Player)
            return;
        $last = $player->getLastDamageCause();

        $killerXuid = null;
        $killer = null;
        $itemInHand = null;
        $message = '';

        if ($last instanceof EntityDamageByEntityEvent || $last instanceof EntityDamageByChildEntityEvent) {
            $damager = $last->getDamager();

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
        }
        
        if ($player->getSession()->getCooldown('spawn.tag') !== null)
            $player->getSession()->removeCooldown('spawn.tag');
        $player->getSession()->addDeath();
        $player->getSession()->setKillStreak(0);
        $player->getSession()->addCooldown('pvp.timer', '&l&aPvP Timer&r&7: &r&c', 60 * 60, true);

        if ($player->getSession()->getFaction() !== null) {
            $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($player->getSession()->getFaction());

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
                $member->setScoreTag(TextFormat::colorize('&6[&c' . $faction->getName() . ' &c' . round($faction->getDtr(), 2) . '■&6]'));
        }

        if ($killer === null) {
            $message = '&c' . $player->getName() . '&4[' . $player->getSession()->getKills() . '] &edied';
            $webhook = $player->getName() . '[' . $player->getSession()->getKills() . '] died';
        } else {
            if (!$itemInHand->isNull() && $itemInHand instanceof Tool) {
                $message = '&c' . $player->getName() . '&4[' . $player->getSession()->getKills() . '] &ewas slain by &c' . $killer . '&4[' . HCFLoader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . '] &cusing ' . $itemInHand->getName();
            } else {
                $message = '&c' . $player->getName() . '&4[' . $player->getSession()->getKills() . '] &ewas slain by &c' . $killer . '&4[' . HCFLoader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . ']';
            }
            $webhook = '`' . $player->getName() . '[' . $player->getSession()->getKills() . '] was slain by ' . $killer . '[' . HCFLoader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . ']`';
        }
        # Construct a discord webhook with its URL
        $webHook = new Webhook(HCFLoader::getInstance()->getConfig()->get('kills.webhook'));

        # Construct a new Message object
        $msg = new Message();
        $msg->setContent($webhook);
        $webHook->send($msg);
        
        $event->setDeathMessage(TextFormat::colorize($message));
    }

    /**
     * @param PlayerExhaustEvent $event
     */
    public function handleExhaust(PlayerExhaustEvent $event): void
    {
        $player = $event->getPlayer();

        if ($player instanceof Player) {
            if ($player->getCurrentClaim() !== null) {
                $claim = HCFLoader::getInstance()->getClaimManager()->getClaim($player->getCurrentClaim());

                if ($claim !== null && $claim->getType() === 'spawn') {
                    $event->cancel();

                    if ($player->getHungerManager()->getFood() !== $player->getHungerManager()->getMaxFood())
                        $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
                    return;
                }
            }

            if ($player->getSession()->hasAutoFeed()) {
                $event->cancel();

                if ($player->getHungerManager()->getFood() !== $player->getHungerManager()->getMaxFood())
                    $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
                return;
            }
        }
    }
    
    /**
     * @param PlayerInteractEvent $event
     * @priority HIGHEST
     */
    public function handleInteract(PlayerInteractEvent $event)
    {
        $action = $event->getAction();
        $block = $event->getBlock();
        $player = $event->getPlayer();
        $item = $event->getItem();
        
        if (!$player instanceof Player)
            return;

        if ($player->getPosition()->distance($player->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn()->asVector3()) < 170) {
            if ($item->getId() === VanillaItems::WATER_BUCKET()->getId()) {
                $event->cancel();
            }

            if ($item->getId() === VanillaItems::DIAMOND_SHOVEL()->getId()) {
                $event->cancel();
            }
            
            if ($item->getId() === VanillaItems::GOLDEN_SHOVEL()->getId()) {
                $event->cancel();
            }
            
            if ($item->getId() === VanillaItems::IRON_SHOVEL()->getId()) {
                $event->cancel();
            }
            
            if ($item->getId() === VanillaItems::STONE_SHOVEL()->getId()) {
                $event->cancel();
            }
            
            if ($item->getId() === VanillaItems::WOODEN_SHOVEL()->getId()) {
                $event->cancel();
            }

            if ($item->getId() === VanillaItems::DIAMOND_HOE()->getId()) {
                $event->cancel();
            }
            
            if ($item->getId() === VanillaItems::GOLDEN_HOE()->getId()) {
                $event->cancel();
            }
            
            if ($item->getId() === VanillaItems::IRON_HOE()->getId()) {
                $event->cancel();
            }
            
            if ($item->getId() === VanillaItems::STONE_HOE()->getId()) {
                $event->cancel();
            }
            
            if ($item->getId() === VanillaItems::WOODEN_HOE()->getId()) {
                $event->cancel();
            }
            
            if ($item->getId() === VanillaItems::LAVA_BUCKET()->getId()) {
                $event->cancel();
            }
        }

        if ($item->getId() === VanillaItems::FLINT_AND_STEEL()->getId()){
            $event->cancel();
        }
    }

    /**
     * @param PlayerItemConsumeEvent $event
     */
    public function handleItemConsume(PlayerItemConsumeEvent $event): void
    {
        /** @var Player */
        $player = $event->getPlayer();
        $item = $event->getItem();

        if($player instanceof Player)

        if ($event->isCancelled())
            return;

        if ($item->getId() == 322) {
            if ($player->getSession()->getCooldown('apple') !== null) {
                $event->cancel();
                return;
            }
            $player->getSession()->addCooldown('apple', '&l&eApple: &r&7', 15);
        } elseif ($item->getId() == 466) {
            if ($player->getSession()->getCooldown('apple.enchanted') !== null) {
                $event->cancel();
                return;
            }
            $player->getSession()->addCooldown('apple.enchanted', '&l&6Gapple&r&7: &r&c', 3600);
        }
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function handleJoin(PlayerJoinEvent $event): void
    {
        /** @var Player */
        $player = $event->getPlayer();
        $player->join();

        $joinMessage = str_replace('{player}', $player->getName(), HCFLoader::getInstance()->getConfig()->get('join.message'));
        $event->setJoinMessage(TextFormat::colorize($joinMessage));
    }

    /**
     * @param PlayerLoginEvent $event
     */
    public function handleLogin(PlayerLoginEvent $event): void
    {
        $player = $event->getPlayer();
        $session = HCFLoader::getInstance()->getSessionManager()->getSession($player->getXuid());

        if ($session === null)
            HCFLoader::getInstance()->getSessionManager()->addSession($player->getXuid(), [
                'name' => $player->getName(),
                'faction' => null,
                'balance' => 0,
                'crystals' => 0,
                'cooldowns' => [],
                'energies' => [],
                'stats' => [
                    'kills' => 0,
                    'deaths' => 0,
                    'killStreak' => 0,
                    'highestKillStreak' => 0
                ]
            ]);
        else {
            if ($player->getName() !== $session->getName())
                $session->setName($player->getName());
        }
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function handleQuit(PlayerQuitEvent $event): void
    {
        /** @var Player */
        $player = $event->getPlayer();
        
        if (!$player instanceof Player) 
            return;
            
        $quitMessage = str_replace('{player}', $player->getName(), HCFLoader::getInstance()->getConfig()->get('quit.message'));
        $disconnectedManager = HCFLoader::getInstance()->getDisconnectedManager();

        if ($player->getSession() !== null && $player->getSession()->getFaction() !== null) {
            $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($player->getSession()->getFaction());
            $faction->announce(TextFormat::colorize("&cMember offline: &f" . $player->getName() . "\n&cDTR: &f" . $faction->getDtr()));
        }
        
        if ($player->getSession() !== null && !$player->getSession()->isLogout()) {
            if ($player->getCurrentClaim() !== null) {
                $claim = HCFLoader::getInstance()->getClaimManager()->getClaim($player->getCurrentClaim());
                
                if ($claim->getType() !== 'spawn') {
                    $disconnectedManager->addDisconnected($player);
                }
            } else {
                $disconnectedManager->addDisconnected($player);
            }
        }
        $event->setQuitMessage(TextFormat::colorize($quitMessage));
    }
}