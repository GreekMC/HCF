<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class UnrallySubCommand implements FactionSubCommand
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
        $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction());
        
        if ($faction->getRally() === null) {
            $sender->sendMessage(TextFormat::colorize('&cNo one from your faction used the rally'));
            return;
        }
        $faction->setRally(null);
        $sender->sendMessage(TextFormat::colorize('&aYou eliminated the rally'));
    }
}