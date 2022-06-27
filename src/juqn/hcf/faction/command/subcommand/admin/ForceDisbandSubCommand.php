<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand\admin;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class ForceDisbandSubCommand
 * @package juqn\hcf\faction\command\subcommand\admin
 */
class ForceDisbandSubCommand implements FactionSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;


        if (!$sender->hasPermission('forcedisband.permission')) {
            return;
        }
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&cUse /faction forcedisband [string: name]'));
            return;
        }

        $name = $args[0];

        if (HCFLoader::getInstance()->getFactionManager()->getFaction($name) === null) {
            $sender->sendMessage(TextFormat::colorize('&cThere is no faction you\'re trying to change the dtr'));
            return;
        }
        $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($name);
        $faction->disband();
        HCFLoader::getInstance()->getFactionManager()->removeFaction($name);
        $sender->sendMessage(TextFormat::colorize('&aThe ' . $name .' &afaction was disbanded'));
    }
}
