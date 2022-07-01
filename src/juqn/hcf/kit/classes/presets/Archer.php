<?php

declare(strict_types=1);

namespace juqn\hcf\kit\classes\presets;

use juqn\hcf\kit\classes\HCFClass;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class Archer extends HCFClass
{
    
    /** @var array */
    private array $archerMark = []
    
    /**
     * Archer construct.
     */
    public function __construct()
    {
        parent::__construct(self::ARCHER);
    }
    
    /**
     * @return Item[]
     */
    public function getArmorItems(): array
    {
        return [
            VanillaItems::LEATHER_CAP(),
            VanillaItems::LEATHER_TUNIC(),
            VanillaItems::LEATHER_PANTS(),
            VanillaItems::LEATHER_BOOTS()
        ];
    }
    
    /**
     * @return EffectInstance[]
     */
    public function getEffects(): array
    {
        return [
            new EffectInstance(VanillaEffects::SPEED(), 20 * 15, 2),
            new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 15, 1),
            new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 20 * 15, 0)
        ];
    }
    
    /**
     * @param EntityDamageEvent $event
     */
    public function handleDamage(EntityDamageEvent $event): void
    {
        $player = $event->getEntity();
        
        if ($event->isCancelled())
            return;

        if ($player instanceof Player) {
            if (isset($this->archerMark[$player->getName()]) && $this->archerMark[$player->getName()] > time()) {
            // if (HCFLoader::getInstance()->inTag($tag, $player->getName())) {
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
        $damager = $event->getDamager();
        $entity = $event->getEntity();

        if ($entity instanceof Player && $damager instanceof Player) {
            if ($damager->getClass() === null)
                return;

            if ($damager->getClass()->getId() === HCFClass::ARCHER) {
                if (!$child instanceof Arrow)
                    return;
                    
                if ($entity->getClass() !== null && $entity->getClass()->getId() === HCFClass::ARCHER) {
                    $damager->sendMessage(TextFormat::colorize('&cYou can\'t archer tag someone who has the same class as you!'));
                    return;
                }

                if ($damager->getSession()->getCooldown('starting.timer') !== null || $damager->getSession()->getCooldown('pvp.timer') !== null)
                    return;

                if ($damager->getCurrentClaim() === 'Spawn')
                    return;
                    
                if ($damager->getSession()->getFaction() === $entity->getSession()->getFaction())
                    return;
                $damager->sendMessage(TextFormat::colorize('&e[&9Archer Range &e(&c' . intva($entity->getPosition()->distance($damager->getPosition())) . '&e)] &6Marked player for 10 seconds.'));
                
                $entity->sendMessage(TextFormat::colorize('&c&lMarked! &r&eAn archer has shot you and marked you (+15% damage) for 10 seconds).'));
                $entity->setNameTag(TextFormat::colorize('&e' . $entity->getName()))
                $entity->getSession()->addCooldown('archer.mark', '&l&6Archer Mark&r&7: &r&c', 10);
                $this->archerMark[$entity->getName()] = time() + 10;
                
                HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($entity): void {
                    if ($entity->isOnline())
                        $entity->setNameTag(TextFormat::colorize('&c' . $entity->getName()));
                }), 20 * 5);
            }
        }
    }
    
    /**
     * @param PlayerItemUseEvent $event
     */
    public function handleItemUse(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();

        if ($player instanceof Player) {
            if ($player->getClass() === null)
                return;
                
            if ($player->getClass()->getId() === HCFClass::ARCHER) {
                if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null)
                    return;
            
                if ($player->getCurrentClaim() === 'Spawn')
                    return;
                    
                if ($item->getId() === VanillaItems::SUGAR()->getId()) {
                    if ($player->getSession()->getCooldown('speed.cooldown') !== null)
                        return;
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 7, 3));
                    $player->getSession()->addCooldown('speed.cooldown', '&l&bSpeed&r&7: &r&c', 60);
                    $player->sendMessage(TextFormat::colorize('&eYou have used your &dSpeed IV Buff'));
                    
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                } elseif ($item->getId() === VanillaItems::FEATHER()->getId()) {
                    if ($player->getSession()->getCooldown('jump.cooldown') !== null)
                        return;
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 7, 7));
                    $player->getSession()->addCooldown('jump.cooldown', '&l&bJump Boost&r&7: &r&c', 60);
                    $player->sendMessage(TextFormat::colorize('&eYou have used your &dJump Boost VIII Buff'));
                    
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                }
            }
        }
    }
}