<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

/**
 * Class SetHomeSubCommand
 * @package juqn\hcf\faction\command\subcommand
 */
class SetHomeSubCommand implements FactionSubCommand
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
        
        if (!in_array($faction->getRole($sender->getXuid()), ['leader', 'co-leader'])) {
            $sender->sendMessage(TextFormat::colorize('&cYou aren\'t the leader or co-leader of the faction to set home'));
            return;
        }
        $claim = HCFLoader::getInstance()->getClaimManager()->insideClaim($sender->getPosition());
        
        if ($claim === null || $claim->getName() !== $faction->getName()) {
            $sender->sendMessage(TextFormat::colorize('&cYou cannot place home outside your claim'));
            return;
        }
        $faction->setHome($sender->getPosition());
        $sender->sendMessage(TextFormat::colorize('&aYou have placed the home of the faction'));
    }
}