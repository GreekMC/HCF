<?php

declare(strict_types=1);

namespace juqn\hcf\koth\command;

use pocketmine\command\CommandSender;

/**
 * Interface KothSubCommand
 * @package juqn\hcf\koth\command
 */
interface KothSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void;
}