<?php

declare(strict_types=1);

namespace juqn\hcf\kit\classes\presets;

use juqn\hcf\kit\classes\HCFClass;
use juqn\hcf\player\Player;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;

class Bard extends HCFClass
{

    /**
     * Bard construct.
     */
    public function __construct()
    {
        parent::__construct(self::BARD);
    }

    /**
     * @return Item[]
     */
    public function getArmorItems(): array
    {
        return [
            VanillaItems::GOLDEN_HELMET(),
            VanillaItems::GOLDEN_CHESTPLATE(),
            VanillaItems::GOLDEN_LEGGINGS(),
            VanillaItems::GOLDEN_BOOTS()
        ];
    }

    /**
     * @return EffectInstance[]
     */
    public function getEffects(): array
    {
        return [
            new EffectInstance(VanillaEffects::SPEED(), 20 * 15, 1),
            new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 15, 0),
            new EffectInstance(VanillaEffects::REGENERATION(), 20 * 15, 0)
        ];
    }
    
    /**
     * @param PlayerItemHeldEvent $event
     */
    public function handleItemHeld(PlayerItemHeldEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();

        if ($player instanceof Player) {
            if ($player->getClass() === null)
                return;
                
            if ($player->getClass()->getId() === HCFClass::BARD) {
                if ($player->getSession()->getCooldown('bard.cooldown') !== null)
                    return;
                    
                if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null)
                    return;
            
                if ($player->getCurrentClaim() === 'Spawn')
                    return;
                    
                if ($item->getId() === VanillaItems::MAGMA_CREAM()->getId()) {
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 20 * 7, 1));
                    
                    if ($player->getSession()->getFaction() !== null) {
                        $players = array_filter($player->getServer()->getOnlinePlayers(), function ($target) use ($player): bool {
                             return $target instanceof Player && $player->getPosition()->distance($target->getPosition()) <= 20 && $player->getSession()->getFaction() === $target->getSession()->getFaction();
                        });
                       
                        if (count($players) !== 0) {
                            foreach ($players as $target) {
                                $target->getEffects()->add(new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 20 * 7, 1));
                            }
                        }
                    }
                } elseif ($item->getId() === VanillaItems::INK_SAC()->getId()) {
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::INVISIBILITY(), 20 * 7, 0));
                    
                    if ($player->getSession()->getFaction() !== null) {
                        $players = array_filter($player->getServer()->getOnlinePlayers(), function ($target) use ($player): bool {
                             return $target instanceof Player && $player->getPosition()->distance($target->getPosition()) <= 20 && $player->getSession()->getFaction() === $target->getSession()->getFaction();
                        });
                        
                        if (count($players) !== 0) {
                            foreach ($players as $target) {
                                $target->getEffects()->add(new EffectInstance(VanillaEffects::INVISIBILITY(), 20 * 7, 0));
                            }
                        }
                    }
                } elseif ($item->getId() === VanillaItems::BLAZE_POWDER()->getId()) {
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 7, 0));
                    
                    if ($player->getSession()->getFaction() !== null) {
                        $players = array_filter($player->getServer()->getOnlinePlayers(), function ($target) use ($player): bool {
                             return $target instanceof Player && $player->getPosition()->distance($target->getPosition()) <= 20 && $player->getSession()->getFaction() === $target->getSession()->getFaction();
                        });
                       
                        if (count($players) !== 0) {
                            foreach ($players as $target) {
                                $target->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 7, 0));
                            }
                        }
                    }
                } elseif ($item->getId() === VanillaItems::IRON_INGOT()->getId()) {
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 7, 0));
                    
                    if ($player->getSession()->getFaction() !== null) {
                        $players = array_filter($player->getServer()->getOnlinePlayers(), function ($target) use ($player): bool {
                            return $target instanceof Player && $player->getPosition()->distance($target->getPosition()) <= 20 && $player->getSession()->getFaction() === $target->getSession()->getFaction();
                        });
                       
                       if (count($players) !== 0) {
                            foreach ($players as $target) {
                                $target->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 7, 0));
                            }
                        }
                    }
                } elseif ($item->getId() === VanillaItems::SUGAR()->getId()) {
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 7, 1));
                    
                    if ($player->getSession()->getFaction() !== null) {
                        $players = array_filter($player->getServer()->getOnlinePlayers(), function ($target) use ($player): bool {
                            return $target instanceof Player && $player->getPosition()->distance($target->getPosition()) <= 20 && $player->getSession()->getFaction() === $target->getSession()->getFaction();
                        });
                       
                        if (count($players) !== 0) {
                            foreach ($players as $target) {
                                $target->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 7, 1));
                            }
                        }
                    }
                } elseif ($item->getId() === VanillaItems::FEATHER()->getId()) {
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 7, 2));
                    
                    if ($player->getSession()->getFaction() !== null) {
                        $players = array_filter($player->getServer()->getOnlinePlayers(), function ($target) use ($player): bool {
                            return $target instanceof Player && $player->getPosition()->distance($target->getPosition()) <= 20 && $player->getSession()->getFaction() === $target->getSession()->getFaction();
                        });
                       
                        if (count($players) !== 0) {
                            foreach ($players as $target) {
                                $target->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 7, 2));
                            }
                        }
                    }
                } elseif ($item->getId() === VanillaItems::GHAST_TEAR()->getId()) {
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 7, 1));
                    
                    if ($player->getSession()->getFaction() !== null) {
                        $players = array_filter($player->getServer()->getOnlinePlayers(), function ($target) use ($player): bool {
                            return $target instanceof Player && $player->getPosition()->distance($target->getPosition()) <= 20 && $player->getSession()->getFaction() === $target->getSession()->getFaction();
                        });
                       
                        if (count($players) !== 0) {
                            foreach ($players as $target) {
                                $target->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 7, 1));
                            }
                        }
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
                
            if ($player->getClass()->getId() === HCFClass::BARD) {
                if ($player->getSession()->getEnergy('bard.energy') === null)
                    return;
                $energy = $player->getSession()->getEnergy('bard.energy');
                
                if ($player->getSession()->getCooldown('bard.cooldown') !== null)
                    return;
                    
                if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null)
                    return;
            
                if ($player->getCurrentClaim() === 'Spawn')
                    return;
                    
                if ($item->getId() === VanillaItems::SPIDER_EYE()->getId()) {
                    if ($energy->getEnergy() < 35)
                        return;
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::WITHER(), 20 * 7, 1));
                    $players = array_filter($player->getServer()->getOnlinePlayers(), function ($target) use ($player): bool {
                         return $target instanceof Player && $player->getPosition()->distance($target->getPosition()) <= 20;
                    });
                       
                    if (count($players) !== 0) {
                        foreach ($players as $target) {
                            $target->getEffects()->add(new EffectInstance(VanillaEffects::WITHER(), 20 * 7, 1));
                            $target->sendMessage(TextFormat::colorize('&eThe bard (&a' . $player->getName() . '&e) has used &bWither II'));
                        }
                    }
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                    $player->getSession()->addCooldown('bard.cooldown', '&l&eBard Effect&r&7: &r&c', 10);
                    $energy->reduceEnergy(35);
                } elseif ($item->getId() === VanillaItems::BLAZE_POWDER()->getId()) {
                    if ($energy->getEnergy() < 40)
                        return;
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 7, 1));
                    
                    if ($player->getSession()->getFaction() !== null) {
                        $players = array_filter($player->getServer()->getOnlinePlayers(), function ($target) use ($player): bool {
                             return $target instanceof Player && $player->getPosition()->distance($target->getPosition()) <= 20 && $player->getSession()->getFaction() === $target->getSession()->getFaction();
                        });
                       
                        if (count($players) !== 0) {
                            foreach ($players as $target) {
                                $target->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 7, 1));
                                $target->sendMessage(TextFormat::colorize('&eThe bard in your faction (&a' . $player->getName() . '&e) has used &bStrenght II'));
                            }
                        }
                    }
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                    $player->getSession()->addCooldown('bard.cooldown', '&l&eBard Effect&r&7: &r&c', 10);
                    $energy->reduceEnergy(40);
                } elseif ($item->getId() === VanillaItems::IRON_INGOT()->getId()) {
                    if ($energy->getEnergy() < 35)
                        return;
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 7, 2));
                    
                    if ($player->getSession()->getFaction() !== null) {
                        $players = array_filter($player->getServer()->getOnlinePlayers(), function ($target) use ($player): bool {
                            return $target instanceof Player && $player->getPosition()->distance($target->getPosition()) <= 20 && $player->getSession()->getFaction() === $target->getSession()->getFaction();
                        });
                       
                       if (count($players) !== 0) {
                            foreach ($players as $target) {
                                $target->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 7, 2));
                                $target->sendMessage(TextFormat::colorize('&eThe bard in your faction (&a' . $player->getName() . '&e) has used &bResistance III'));
                            }
                        }
                    }
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                    $player->getSession()->addCooldown('bard.cooldown', '&l&eBard Effect&r&7: &r&c', 10);
                    $energy->reduceEnergy(35);
                } elseif ($item->getId() === VanillaItems::SUGAR()->getId()) {
                    if ($energy->getEnergy() < 20)
                        return;
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 7, 2));
                    
                    if ($player->getSession()->getFaction() !== null) {
                        $players = array_filter($player->getServer()->getOnlinePlayers(), function ($target) use ($player): bool {
                            return $target instanceof Player && $player->getPosition()->distance($target->getPosition()) <= 20 && $player->getSession()->getFaction() === $target->getSession()->getFaction();
                        });
                       
                        if (count($players) !== 0) {
                            foreach ($players as $target) {
                                $target->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 7, 2));
                                $target->sendMessage(TextFormat::colorize('&eThe bard in your faction (&a' . $player->getName() . '&e) has used &bSpeed III'));
                            }
                        }
                    }
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                    $player->getSession()->addCooldown('bard.cooldown', '&l&eBard Effect&r&7: &r&c', 10);
                    $energy->reduceEnergy(20);
                } elseif ($item->getId() === VanillaItems::FEATHER()->getId()) {
                    if ($energy->getEnergy() < 30)
                        return;
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 7, 7));
                    
                    if ($player->getSession()->getFaction() !== null) {
                        $players = array_filter($player->getServer()->getOnlinePlayers(), function ($target) use ($player): bool {
                            return $target instanceof Player && $player->getPosition()->distance($target->getPosition()) <= 20 && $player->getSession()->getFaction() === $target->getSession()->getFaction();
                        });
                       
                        if (count($players) !== 0) {
                            foreach ($players as $target) {
                                $target->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 7, 7));
                                $target->sendMessage(TextFormat::colorize('&eThe bard in your faction (&a' . $player->getName() . '&e) has used &bJump Boost VIII'));
                            }
                        }
                    }
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                    $player->getSession()->addCooldown('bard.cooldown', '&l&eBard Effect&r&7: &r&c', 10);
                    $energy->reduceEnergy(30);
                } elseif ($item->getId() === VanillaItems::GHAST_TEAR()->getId()) {
                    if ($energy->getEnergy() < 35)
                        return;
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 7, 2));
                    
                    if ($player->getSession()->getFaction() !== null) {
                        $players = array_filter($player->getServer()->getOnlinePlayers(), function ($target) use ($player): bool {
                            return $target instanceof Player && $player->getPosition()->distance($target->getPosition()) <= 20 && $player->getSession()->getFaction() === $target->getSession()->getFaction();
                        });
                       
                        if (count($players) !== 0) {
                            foreach ($players as $target) {
                                $target->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 7, 2));
                                $target->sendMessage(TextFormat::colorize('&eThe bard in your faction (&a' . $player->getName() . '&e) has used &bRegeneration III'));
                            }
                        }
                    }
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                    $player->getSession()->addCooldown('bard.cooldown', '&l&eBard Effect&r&7: &r&c', 10);
                    $energy->reduceEnergy(35);
                }
            }
        }
    }
}