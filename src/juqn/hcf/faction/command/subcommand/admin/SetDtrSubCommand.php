<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand\admin;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class SetDtrSubCommand
 * @package juqn\hcf\faction\command\subcommand\admin
 */
class SetDtrSubCommand implements FactionSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction());

        if (empty($args[0])) {
            $sender->sendMessage('&cUse /faction setdtr [string: name] [int: dtr]');
            return;
        }
        if (!is_numeric($args[1])) {
            $sender->sendMessage('&cUse /faction setdtr [string: name] [int: dtr]');
            return;
        }
        $name = $args[0];
        $factionName = null;
        $player = $sender->getServer()->getPlayerByPrefix($name);

        if ($player instanceof Player) {
            if ($player->getSession()->getFaction() === null) {
                $sender->sendMessage(TextFormat::colorize('&cThe player you\'re trying to focus has no faction'));
                return;
            }

            $factionName = $player->getSession()->getFaction();
        } else {
            if (HCFLoader::getInstance()->getFactionManager()->getFaction($name) === null) {
                $sender->sendMessage(TextFormat::colorize('&cThere is no faction you\'re set the dtr'));
                return;
            }
            $factionName = $name;
        }
        HCFLoader::getInstance()->getFactionManager()->getFaction($factionName)->setDtr($args[1]);
        $sender->sendMessage(TextFormat::colorize('&aThe DTR of the faction ' . $factionName . ' is now ' . $args[1]));
    }
}