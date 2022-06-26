<?php

declare(strict_types=1);

namespace juqn\hcf\command;

use juqn\hcf\player\Player;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class GodCommand
 * @package juqn\hcf\command
 */
class GodCommand extends Command
{
    
    /**
     * GodCommand construct.
     */
    public function __construct()
    {
        parent::__construct('god', 'Use command for god');
        $this->setPermission('god.command');
    }
    
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player)
            return;
            
        if (!$this->testPermission($sender))
            return;
        
        if ($sender->isGod()) {
            $sender->setGod(false);
            $sender->sendMessage(TextFormat::colorize('&cGod disabled'));
        } else {
            $sender->setGod(true);
            $sender->sendMessage(TextFormat::colorize('&aGod enabled'));
        }
    }
}