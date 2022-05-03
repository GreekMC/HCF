<?php

declare(strict_types=1);

namespace juqn\hcf\kit\command;

use pocketmine\command\CommandSender;

/**
 * Interface KitSubCommand
 * @package juqn\hcf\kit\command
 */
interface KitSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void;
}