<?php

declare(strict_types=1);

namespace juqn\hcf\reclaim\command;

use pocketmine\command\CommandSender;

/**
 * Interface ReclaimSubCommand
 * @package juqn\hcf\reclaim\command
 */
interface ReclaimSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void;
}