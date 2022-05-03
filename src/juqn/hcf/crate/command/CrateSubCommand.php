<?php

declare(strict_types=1);

namespace juqn\hcf\crate\command;

use pocketmine\command\CommandSender;

/**
 * Interface CrateSubCommand
 * @package juqn\hcf\crate\command
 */
interface CrateSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void;
}