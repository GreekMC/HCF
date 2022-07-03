<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command;

use pocketmine\command\CommandSender;

interface FactionSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void;
}