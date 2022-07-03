<?php

declare(strict_types=1);

namespace juqn\hcf\command;

use juqn\hcf\player\Player;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

/**
 * Class PvPCommand
 * @package juqn\hcf\command
 */
class TLCommand extends Command
{
    
    /**
     * PvPCommand construct.
     */
    public function __construct()
    {
        parent::__construct('tl', 'Use it to send your coordinates to your faction');
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

        if ($sender->getSession()->getFaction() === null) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have a faction'));
            return;
        }

        foreach (Server::getInstance()->getOnlinePlayers() as $online) {
            if ($online instanceof Player)
                if ($online->getSession()->getFaction() === null) {
                    continue;
                }
            if ($online->getSession()->getFaction() === $sender->getSession()->getFaction()) {
                $online->sendMessage("§9(Team) " . $sender->getName() . ": §e[" . (int)$sender->getPosition()->getX() . ", " . (int)$sender->getPosition()->getY() . ", " . (int)$sender->getPosition()->getZ() . "]");
            }
        }
    }
}