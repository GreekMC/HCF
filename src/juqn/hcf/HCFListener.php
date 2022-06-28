<?php

declare(strict_types=1);

namespace juqn\hcf;

use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use juqn\hcf\entity\EnderpearlEntity;
use juqn\hcf\item\EnderpearlItem;
use juqn\hcf\kit\classes\ClassFactory;
use juqn\hcf\kit\classes\HCFClass;
use juqn\hcf\kit\classes\presets\Bard;
use juqn\hcf\player\Player;

use pocketmine\block\FenceGate;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\entity\Location;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\item\VanillaItems;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
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
                            $damager->sendMessage(TextFormat::colorize("§eYou cannot hurt §2" . $entity->getName() . "§e."));
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
        $player->getSession()->removeCooldown('spawn.tag');
        $player->getSession()->addDeath();
        $player->getSession()->setKillStreak(0);

        $player->getSession()->addCooldown('pvp.timer', '&l&aPvP Timer&r&7: &r&c', 60 * 60, true);

        if ($player->getSession()->getFaction() !== null) {
            $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($player->getSession()->getFaction());
            
            $faction->setPoints($faction->getPoints() - 1);
            $faction->setDtr($faction->getDtr() - 1.0);
            // $faction->setTimeRegeneration(45 * 60);
            
            $faction->announce(TextFormat::colorize('&cMember Death: &f' . $player->getName() . PHP_EOL . '&cDTR: &f' . $faction->getDtr()));
            
            # Faction Raid
            if ($faction->getDtr() < 0.00 && !$faction->isRaided()) {
                $faction->setRaided(true);
                $faction->setPoints($faction->getPoints() - 10);
                
                if ($killerXuid !== null) {
                    $session = HCFLoader::getInstance()->getSessionManager()->getSession($killerXuid);
                
                    if ($session !== null && $session->getFaction()) {
                        $fac = HCFLoader::getInstance()->getFactionManager()->getFaction($session->getFaction());
                        
                        if ($fac !== null) {
                            $fac->setPoints($fac->getPoints() + 3);
                        }
                    }
                }
            }
            
            # Regen time
            if (!$faction->isRaided()) {
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
                $webhook = '' . $player->getName() . '[' . $player->getSession()->getKills() . '] was slain by ' . $killer . '[' . HCFLoader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . '] using ' . $itemInHand->getName();
            } else {
                $message = '&c' . $player->getName() . '&4[' . $player->getSession()->getKills() . '] &ewas slain by &c' . $killer . '&4[' . HCFLoader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . ']';
                $webhook = $player->getName() . '[' . $player->getSession()->getKills() . '] was slain by ' . $killer . '[' . HCFLoader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . ']';
            }
            // Construct a discord webhook with its URL
            $webHook = new Webhook(HCFLoader::getInstance()->getConfig()->get('kills.webhook'));

            // Construct a new Message object
            $msg = new Message();
            $msg->setContent($webhook);
            $webHook->send($msg);
            $event->setDeathMessage(TextFormat::colorize($message));
        }
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
        
        if (!$player instanceof Player) return;
        $quitMessage = str_replace('{player}', $player->getName(), HCFLoader::getInstance()->getConfig()->get('quit.message'));
        $disconnectedManager = HCFLoader::getInstance()->getDisconnectedManager();

        if ($player->getSession() !== null && $player->getSession()->getFaction() !== null) {
            $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($player->getSession()->getFaction());
            $faction->announce(TextFormat::colorize("&cMember offline: &f" . $player->getName() . "\n&cDTR: &f" . $faction->getDtr()));
        }
        
        if (!$player->getSession()->isLogout()) {
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
    
    /**
     * @param EntityDamageEvent $event
     */
    public function handleArcherTag(EntityDamageEvent $event): void
    {
        $tag = 'ArcherMark';
        $player = $event->getEntity();
        
        if ($event->isCancelled())
            return;

        if ($player instanceof Player) {
            if (HCFLoader::getInstance()->inTag($tag, $player->getName())) {
                $baseDamage = $event->getBaseDamage();
                $event->setBaseDamage($baseDamage + 1.5);
            }
        }
    }

    /**
     * @param EntityDamageByChildEntityEvent $event
     */
    public function handleDamageByChildEntity(EntityDamageByChildEntityEvent $event): void
    {
        $child = $event->getChild();
        $entity = $event->getEntity();
        $damager = $event->getDamager();

        $tag = 'ArcherMark';

        if (!$entity instanceof Player || !$damager instanceof Player)
            return;

        if ($damager->getClass() === null) {
            return;
        }

        if ($damager->getClass()->getId() === HCFClass::ARCHER) {
            if ($child instanceof Arrow) {

                if ($entity->getClass()->getId() === HCFClass::ARCHER){
                    $damager->sendMessage("You can't archer tag someone who has the same class as you!");
                    return;
                }
                if ($damager->getSession()->getCooldown('starting.timer') !== null || $damager->getSession()->getCooldown('pvp.timer') !== null) {
                    return;
                }

                if ($damager->getCurrentClaim() === 'Spawn') {
                    return;
                }
                if ($damager->getSession()->getFaction() === $entity->getSession()->getFaction()) {
                    return;
                }


                $damager->sendMessage("§e[§9Archer Range §e(§c" . (int)$entity->getPosition()->distance($damager->getPosition()) . "§e)] §6Marked player for 10 seconds.");
                $entity->sendMessage("§c§lMarked! §r§eAn archer has shot you and marked you (+15% damage) for 10 seconds).");
                $entity->setNameTag("§e" . $entity->getName());

                $entity->getSession()->addCooldown('archer.mark', '&l&6Archer Mark&r&7: &r&c', 10);

                HCFLoader::getInstance()->setTag($tag, $entity->getName(), 10);
                HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($entity): void {
                    if ($entity->isOnline()) {
                        $entity->setNameTag("§c" . $entity->getName());
                    }
                }), 20 * 5);
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
            
            if ($item->getId() === VanillaItems::LAVA_BUCKET()->getId()) {
                $event->cancel();
            }
        }

        if ($item->getId() === VanillaItems::FLINT_AND_STEEL()->getId()){
            $event->cancel();
        }
    }
    
    /**
     * @param PlayerItemUseEvent $event
     */
    public function handleEffects(PlayerItemUseEvent $event):void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if (!$player instanceof Player)
            return;

        if($item instanceof Armor){
            $event->cancel();
        }

        if ($player->getClass() === null) {
            return;
        }

        if ($player->getClass()->getId() === HCFClass::ROGUE) {
            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                return;
            }
            
            if ($player->getCurrentClaim() === 'Spawn') {
                return;
            }

            if ($item->getId() === VanillaItems::SUGAR()->getId()) {
                if ($player->getSession()->getCooldown('speed.cooldown') !== null) {
                    return;
                }
                $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 7, 3));
                $item = $player->getInventory()->getItemInHand();
                $item->pop();
                $player->getInventory()->setItemInHand($item);
                $player->getSession()->addCooldown('speed.cooldown', '&l&bSpeed&r&7: &r&c', 60);
                $player->sendMessage("§eYou have used your §dSpeed IV Buff");
            }
            if ($item->getId() === VanillaItems::FEATHER()->getId()) {
                if ($player->getSession()->getCooldown('jump.cooldown') !== null) {
                    return;
                }
                $player->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 7, 7));
                $item = $player->getInventory()->getItemInHand();
                $item->pop();
                $player->getInventory()->setItemInHand($item);
                $player->getSession()->addCooldown('jump.cooldown', '&l&bJump Boost&r&7: &r&c', 60);
                $player->sendMessage("§eYou have used your §dJump Boost VIII Buff");
            }
        }

        if ($player->getClass()->getId() === HCFClass::ARCHER) {
            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                return;
            }
            
            if ($player->getCurrentClaim() === 'Spawn') {
                return;
            }
            
            if ($item->getId() === VanillaItems::SUGAR()->getId()) {
                if ($player->getSession()->getCooldown('speed.cooldown') !== null) {
                    return;
                }
                $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 7, 3));
                $item = $player->getInventory()->getItemInHand();
                $item->pop();
                $player->getInventory()->setItemInHand($item);
                $player->getSession()->addCooldown('speed.cooldown', '&l&bSpeed&r&7: &r&c', 60);
                $player->sendMessage("§eYou have used your §dSpeed IV Buff");
            }
            
            if ($item->getId() === VanillaItems::FEATHER()->getId()) {
                if ($player->getSession()->getCooldown('jump.cooldown') !== null) {
                    return;
                }
                $player->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 7, 7));
                $item = $player->getInventory()->getItemInHand();
                $item->pop();
                $player->getInventory()->setItemInHand($item);
                $player->getSession()->addCooldown('jump.cooldown', '&l&bJump Boost&r&7: &r&c', 60);
                $player->sendMessage("§eYou have used your §dJump Boost VIII Buff");
            }
        }

        if ($player->getClass()->getId() === HCFClass::BARD) {
            if ($player->getSession()->getCooldown('bard.cooldown') !== null) {
                return;
            }
            
            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                return;
            }
            
            if ($player->getCurrentClaim() === 'Spawn') {
                return;
            }

            switch ($item->getId()) {
                case VanillaItems::SPIDER_EYE()->getId():
                    if($player->getSession()->getEnergy('bard.energy')->getEnergy() > 35){
                        $player->getEffects()->add(new EffectInstance(VanillaEffects::WITHER(), 20 * 7, 1));
                        
                        foreach (Server::getInstance()->getOnlinePlayers() as $online_player) {
                            if ($player->getPosition()->distance($online_player->getPosition()) <= 20) {
                                $online_player->getEffects()->add(new EffectInstance(VanillaEffects::WITHER(), 20 * 7, 1));
                                $online_player->sendMessage("§eThe bard (§a" . $player->getName() . "§e) has used §bWither II");
                            }
                        }
                        $item = $player->getInventory()->getItemInHand();
                        $item->pop();
                        $player->getInventory()->setItemInHand($item);
                        $player->getSession()->addCooldown('bard.cooldown', '&l&eBard Effect&r&7: &r&c', 10);
                        $player->getSession()->getEnergy('bard.energy')->reduceEnergy(35);
                    }
                    break;
                    
                case VanillaItems::BLAZE_POWDER()->getId():
                    if($player->getSession()->getEnergy('bard.energy')->getEnergy() > 40) {
                        $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 7, 1));
                        foreach (Server::getInstance()->getOnlinePlayers() as $online_player) {
                            if ($player->getPosition()->distance($online_player->getPosition()) <= 20) {
                                if ($online_player instanceof Player)
                                    if ($online_player->getSession()->getFaction() === $player->getSession()->getFaction()) {
                                        $online_player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 7, 1));
                                        $online_player->sendMessage("§eThe bard in your faction (§a" . $player->getName() . "§e) has used §bStrenght II");
                                    }
                            }
                        }
                        $item = $player->getInventory()->getItemInHand();
                        $item->pop();
                        $player->getInventory()->setItemInHand($item);
                        $player->getSession()->addCooldown('bard.cooldown', '&l&eBard Effect&r&7: &r&c', 10);
                        $player->getSession()->getEnergy('bard.energy')->reduceEnergy(40);
                    }
                    break;
                    
                case VanillaItems::IRON_INGOT()->getId():
                    if($player->getSession()->getEnergy('bard.energy')->getEnergy() > 35) {
                        $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 7, 2));
                        
                        foreach (Server::getInstance()->getOnlinePlayers() as $online_player) {
                            if ($player->getPosition()->distance($online_player->getPosition()) <= 20) {
                                if ($online_player instanceof Player) {
                                    if ($online_player->getSession()->getFaction() === $player->getSession()->getFaction()) {
                                        $online_player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 7, 2));
                                        $online_player->sendMessage("§eThe bard in your faction (§a" . $player->getName() . "§e) has used §bResistance III");
                                    }
                                }
                            }
                        }
                        $item = $player->getInventory()->getItemInHand();
                        $item->pop();
                        $player->getInventory()->setItemInHand($item);
                        $player->getSession()->addCooldown('bard.cooldown', '&l&eBard Effect&r&7: &r&c', 10);
                        $player->getSession()->getEnergy('bard.energy')->reduceEnergy(35);
                    }
                    break;
                    
                case VanillaItems::SUGAR()->getId():
                    if ($player->getSession()->getEnergy('bard.energy')->getEnergy() > 20) {
                        $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 7, 2));
                        
                        foreach (Server::getInstance()->getOnlinePlayers() as $online_player) {
                            if ($player->getPosition()->distance($online_player->getPosition()) <= 20) {
                                if ($online_player instanceof Player) {
                                    if ($online_player->getSession()->getFaction() === $player->getSession()->getFaction()) {
                                        $online_player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 7, 2));
                                        $online_player->sendMessage("§eThe bard in your faction (§a" . $player->getName() . "§e) has used §bSpeed III");
                                    }
                                }
                            }
                        }
                        $item = $player->getInventory()->getItemInHand();
                        $item->pop();
                        $player->getInventory()->setItemInHand($item);
                        $player->getSession()->addCooldown('bard.cooldown', '&l&eBard Effect&r&7: &r&c', 10);
                        $player->getSession()->getEnergy('bard.energy')->reduceEnergy(20);
                    }
                    break;
                    
                case VanillaItems::FEATHER()->getId():
                    if($player->getSession()->getEnergy('bard.energy')->getEnergy() > 30) {
                        $player->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 7, 7));
                        
                        foreach (Server::getInstance()->getOnlinePlayers() as $online_player) {
                            if ($player->getPosition()->distance($online_player->getPosition()) <= 20) {
                                if ($online_player instanceof Player) {
                                    if ($online_player->getSession()->getFaction() === $player->getSession()->getFaction()) {
                                        $online_player->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 7, 7));
                                        $online_player->sendMessage("§eThe bard in your faction (§a" . $player->getName() . "§e) has used §bJump Boost VIII");
                                    }
                                }
                            }
                        }
                        $item = $player->getInventory()->getItemInHand();
                        $item->pop();
                        $player->getInventory()->setItemInHand($item);
                        $player->getSession()->addCooldown('bard.cooldown', '&l&eBard Effect&r&7: &r&c', 10);
                        $player->getSession()->getEnergy('bard.energy')->reduceEnergy(30);
                    }
                    break;
                    
                case VanillaItems::GHAST_TEAR()->getId():
                    if($player->getSession()->getEnergy('bard.energy')->getEnergy() > 35) {
                        $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 7, 2));
                        
                        foreach (Server::getInstance()->getOnlinePlayers() as $online_player) {
                            if ($player->getPosition()->distance($online_player->getPosition()) <= 20) {
                                if ($online_player instanceof Player) {
                                    if ($online_player->getSession()->getFaction() === $player->getSession()->getFaction()) {
                                        $online_player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 7, 2));
                                        $online_player->sendMessage("§eThe bard in your faction (§a" . $player->getName() . "§e) has used §bRegeneration III");
                                    }
                                }
                            }
                        }
                        $item = $player->getInventory()->getItemInHand();
                        $item->pop();
                        $player->getInventory()->setItemInHand($item);
                        $player->getSession()->addCooldown('bard.cooldown', '&l&eBard Effect&r&7: &r&c', 10);
                        $player->getSession()->getEnergy('bard.energy')->reduceEnergy(35);
                    }
                    break;
            }
        }
    }

    /**
     * @param PlayerItemHeldEvent $event
     */
    public function handleHoldEffects(PlayerItemHeldEvent $event):void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if (!$player instanceof Player)
            return;

        if ($player->getClass() === null) {
            return;
        }

        if ($player->getClass()->getId() === HCFClass::BARD) {
            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                return;
            }
            
            if ($player->getCurrentClaim() === 'Spawn') {
                return;
            }

            switch ($item->getId()) {
                case VanillaItems::BLAZE_POWDER()->getId():
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 7, 0));
                    
                    foreach (Server::getInstance()->getOnlinePlayers() as $online_player) {
                        if ($player->getPosition()->distance($online_player->getPosition()) <= 20) {
                            if ($online_player instanceof Player) {
                                if ($online_player->getSession()->getFaction() === $player->getSession()->getFaction()) {
                                    $online_player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 7, 0));
                                }
                            }
                        }
                    }
                    break;
                    
                case VanillaItems::IRON_INGOT()->getId():
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 7, 0));
                    
                    foreach (Server::getInstance()->getOnlinePlayers() as $online_player) {
                        if ($player->getPosition()->distance($online_player->getPosition()) <= 20) {
                            if ($online_player instanceof Player) {
                                if ($online_player->getSession()->getFaction() === $player->getSession()->getFaction()) {
                                    $online_player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 7, 0));
                                }
                            }
                        }
                    }
                    break;
                    
                case VanillaItems::SUGAR()->getId():
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 7, 1));
                    
                    foreach (Server::getInstance()->getOnlinePlayers() as $online_player) {
                        if ($player->getPosition()->distance($online_player->getPosition()) <= 20) {
                            if ($online_player instanceof Player) {
                                if ($online_player->getSession()->getFaction() === $player->getSession()->getFaction()) {
                                    $online_player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 7, 1));
                                }
                            }
                        }
                    }
                    break;
                    
                case VanillaItems::FEATHER()->getId():
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 7, 2));
                    
                    foreach (Server::getInstance()->getOnlinePlayers() as $online_player) {
                        if ($player->getPosition()->distance($online_player->getPosition()) <= 20) {
                            if ($online_player instanceof Player) {
                                if ($online_player->getSession()->getFaction() === $player->getSession()->getFaction()) {
                                    $online_player->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 7, 2));
                                }
                            }
                        }
                    }
                    break;
                    
                case VanillaItems::MAGMA_CREAM()->getId():
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 20 * 7, 0));
                    
                    foreach (Server::getInstance()->getOnlinePlayers() as $online_player) {
                        if ($player->getPosition()->distance($online_player->getPosition()) <= 20) {
                            if ($online_player instanceof Player) {
                                if ($online_player->getSession()->getFaction() === $player->getSession()->getFaction()) {
                                    $online_player->getEffects()->add(new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 20 * 7, 0));
                                }
                            }
                        }
                    }
                    break;
                    
                case VanillaItems::GHAST_TEAR()->getId():
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 7, 1));
                    
                    foreach (Server::getInstance()->getOnlinePlayers() as $online_player) {
                        if ($player->getPosition()->distance($online_player->getPosition()) <= 20) {
                            if ($online_player instanceof Player) {
                                if ($online_player->getSession()->getFaction() === $player->getSession()->getFaction()) {
                                    $online_player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 7, 1));
                                }
                            }
                        }
                    }
                    break;
                    
                case VanillaItems::INK_SAC()->getId():
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::INVISIBILITY(), 20 * 7, 0));
                    
                    foreach (Server::getInstance()->getOnlinePlayers() as $online_player) {
                        if ($player->getPosition()->distance($online_player->getPosition()) <= 20) {
                            if ($online_player instanceof Player) {
                                if ($online_player->getSession()->getFaction() === $player->getSession()->getFaction()) {
                                    $online_player->getEffects()->add(new EffectInstance(VanillaEffects::INVISIBILITY(), 20 * 7, 0));
                                }
                            }
                        }
                    }
                    break;
            }
        }
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function handleRogue(EntityDamageEvent $event): void
    {
        $player = $event->getEntity();
        
        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            
            if ($player instanceof Player && $damager instanceof Player) {
                if ($damager->getClass() === null) {
                    return;
                }
                
                if ($damager->getClass()->getId() === HCFClass::ROGUE && $damager->getInventory()->getItemInHand()->getId() === VanillaItems::GOLDEN_SWORD()->getId()) {
                    if ($damager->getViewPos() == $player->getViewPos()) {
                        if ($damager->getSession()->getCooldown('rogue.cooldown') !== null) {
                            return;
                        }
                        $player->setHealth($player->getHealth() - 9);
                        $damager->getInventory()->setItemInHand(VanillaItems::AIR());
                        $damager->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 20 * 3, 0));
                        $damager->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 20 * 3, 3));
                        $damager->sendMessage("§eYou just gave §b§l" . $player->getName() . " §r§eBackstap his current health is §c§l" . $player->getHealth() . " §r§eHP");
                        $damager->getSession()->addCooldown('rogue.cooldown', '&l&dRogue Cooldown&r&7: &r&c', 10);
                    }
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
            if ($player->getSession()->hasFactionChat()) {
                $event->cancel();
            
                foreach (Server::getInstance()->getOnlinePlayers() as $online) {
                    if ($online instanceof Player) {
                        if ($online->getSession()->getFaction() === null) {
                            continue;
                        }
                        
                        if ($online->getSession()->getFaction() === $player->getSession()->getFaction()) {
                            $online->sendMessage("§9(Team) ". $player->getName() . ": §e" . $message);
                        }
                    }
                }
                return;
            }
        }
    }
}
