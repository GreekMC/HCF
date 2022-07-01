<?php

declare(strict_types=1);

namespace juqn\hcf\kit\classes\presets;

use juqn\hcf\kit\classes\HCFClass;
use juqn\hcf\player\Player;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;

class Rogue extends HCFClass
{

    /**
     * Rogue construct.
     */
    public function __construct()
    {
        parent::__construct(self::ROGUE);
    }

    /**
     * @return Item[]
     */
    public function getArmorItems(): array
    {
        return [
            VanillaItems::CHAINMAIL_HELMET(),
            VanillaItems::CHAINMAIL_CHESTPLATE(),
            VanillaItems::CHAINMAIL_LEGGINGS(),
            VanillaItems::CHAINMAIL_BOOTS()
        ];
    }

    /**
     * @return EffectInstance[]
     */
    public function getEffects(): array
    {
        return [
            new EffectInstance(VanillaEffects::SPEED(), 20 * 15, 2),
            new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 15, 0),
            new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 15, 3)
        ];
    }
    
    /**
     * @param EntityDamageEvent $event
     */
    public function handleDamage(EntityDamageEvent $event): void
    {
        $entity = $event->getEntity();
        
        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            
            if ($entity instanceof Player && $damager instanceof Player) {
                if ($damager->getClass() === null)
                    return;
                
                if ($damager->getClass()->getId() === HCFClass::ROGUE && $damager->getInventory()->getItemInHand()->getId() === VanillaItems::GOLDEN_SWORD()->getId()) {
                    if ($damager->getCurrentClaim() === 'Spawn'){
                        return;
                    }

                    if ($entity->getCurrentClaim() === 'Spawn'){
                        return;
                    }

                    if ($damager->getSession()->getCooldown('starting.timer') !== null || $damager->getSession()->getCooldown('pvp.timer') !== null) {
                        $event->cancel();
                        return;
                    }
                    
                    if ($entity->getSession()->getCooldown('starting.timer') !== null || $entity->getSession()->getCooldown('pvp.timer') !== null) {
                        $event->cancel();
                        return;
                    }
                    
                    if ($damager->getViewPos() == $entity->getViewPos()) {
                        if ($damager->getSession()->getCooldown('rogue.cooldown') !== null)
                            return;
                        $entity->setHealth($entity->getHealth() - 9);
                        
                        $damager->getInventory()->setItemInHand(VanillaItems::AIR());
                        $damager->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 20 * 3, 0));
                        $damager->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 20 * 3, 3));
                        $damager->sendMessage(TextFormat::colorize('&eYou just gave &b&l' . $entity->getName() . ' &r&eBackstap his current health is &c&l' . $entity->getHealth() . ' &r&eHP'));
                        $damager->getSession()->addCooldown('rogue.cooldown', '&l&dRogue Cooldown&r&7: &r&c', 10);
                    }
                }
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
                
            if ($player->getClass()->getId() === HCFClass::ROGUE) {
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