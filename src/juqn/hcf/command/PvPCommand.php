<?php

declare(strict_types=1);

namespace juqn\hcf\command;

use juqn\hcf\player\Player;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class PvPCommand
 * @package juqn\hcf\command
 */
class PvPCommand extends Command
{
    
    /**
     * PvPCommand construct.
     */
    public function __construct()
    {
        parent::__construct('pvp', 'Use for active pvp');
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
        
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&cUse /pvp enable'));
            return;
        }
        
        switch (strtolower($args[0])) {
            case 'enable':
                if ($sender->getSession()->getCooldown('starting.timer') === null && $sender->getSession()->getCooldown('pvp.timer') === null) {
                    $sender->sendMessage(TextFormat::colorize('&cYou don\'t have starting timer or pvp timer'));
                    return;
                }
                
                if ($sender->getSession()->getCooldown('starting.timer') !== null)
                    $sender->getSession()->removeCooldown('starting.timer');
                
                if ($sender->getSession()->getCooldown('pvp.timer') !== null)
                    $sender->getSession()->removeCooldown('pvp.timer');
                $sender->sendMessage(TextFormat::colorize('&aYou successfully enabled your pvp'));
                break;
            
            default:
                $sender->sendMessage(TextFormat::colorize('&cUse /pvp enable'));
                break;
        }
    }
}