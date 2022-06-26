<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\faction\Faction;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class UnclaimSubCommand implements FactionSubCommand
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
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have a faction'));
            return;
        }
        $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction());
        
        if ($faction->getRole($sender->getXuid()) !== Faction::LEADER) {
            $sender->sendMessage(TextFormat::colorize('&cYou aren\'t the leader can disband the faction'));
            return;
        }
        
        if ($faction->getTimeRegeneration() !== null) {
            $sender->sendMessage(TextFormat::colorize("&cYou can't use this with regeneration time active!"));
            return;
        }
        HCFLoader::getInstance()->getClaimManager()->removeClaim($faction->getName());
        $sender->sendMessage(TextFormat::colorize('&cYou have removed the claim from your faction'));
    }
}