<?php

declare(strict_types=1);

namespace juqn\hcf\enchantment\defaults;

use juqn\hcf\enchantment\Enchantment;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\player\Player;

/**
 * Class FireResistanceEnchantment
 * @package juqn\hcf\enchantment\defaults
 */
class FireResistanceEnchantment extends Enchantment
{
    
    /**
     * FireResistanceEnchantment construct.
     */
    public function __construct()
    {
        parent::__construct('FireResistance', Rarity::COMMON, ItemFlags::ARMOR, ItemFlags::NONE, 2);
    }
    
    /**
     * @param Player $player
     */
    public function giveEffect(Player $player): void
    {
        $player->getEffects()->add(new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 2 * 20, 1, false, false));
    }
}