<?php

declare(strict_types=1);

namespace juqn\hcf\enchantment\command;

use pocketmine\command\CommandSender;

/**
 * Interface EnchantmentSubCommand
 * @package juqn\hcf\enchantment\command
 */
interface EnchantmentSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void;
}