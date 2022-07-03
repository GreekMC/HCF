<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ChatSubCommand implements FactionSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if ($sender->getSession()->getFaction() === null) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\' have a faction'));
            return;
        }

        if ($sender->getSession()->hasFactionChat() === false) {
            $sender->getSession()->setFactionChat(true);
            $sender->sendMessage(TextFormat::GREEN . "You are now in the faction chat!");
        } else {
            $sender->getSession()->setFactionChat(false);
            $sender->sendMessage(TextFormat::RED . "You are now in public chat!");
        }
    }
}