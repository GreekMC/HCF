<?php

declare(strict_types=1);

namespace juqn\hcf\enchantment;

use pocketmine\player\Player;

/**
 * Class Enchantment
 * @package juqn\hcf\enchantment
 */
abstract class Enchantment extends \pocketmine\item\enchantment\Enchantment
{
    
    /**
     * @param Player $player
     */
    public function giveEffect(Player $player): void {}
    
    /**
     * @param Player $player
     */
    public function handleMove(Player $player): void {}
}
