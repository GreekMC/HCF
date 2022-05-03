<?php

declare(strict_types=1);

namespace juqn\hcf\vkit\command;

use pocketmine\command\CommandSender;

/**
 * Interface vKitSubCommand
 * @package juqn\hcf\kit\command
 */
interface vKitSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void;
}