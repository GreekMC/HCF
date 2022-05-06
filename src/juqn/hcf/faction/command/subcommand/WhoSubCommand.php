<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class WhoSubCommand implements FactionSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if (!isset($args[0])) {
            if ($sender->getSession()->getFaction() === null) {
                $sender->sendMessage(TextFormat::colorize('&cYou don\'t have faction'));
                return;
            }
            return;
        }
        $faction = null;
        $target = $sender->getServer()->getPlayerByPrefix($args[0]);

        if ($target instanceof Player) {
            if ($target->getSession()->getFaction() === null) {
                $sender->sendMessage(TextFormat::colorize('Player dont have faction'));
                return;
            }
            $faction = $target->getSession()->getFaction();
        } else {
            if (HCFLoader::getInstance()->getFactionManager()->getFaction($args[0])) {
                $faction = $args[0];
            }
        }

        if ($faction === null)
            return;
    }
}