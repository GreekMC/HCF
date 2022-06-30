<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand\admin;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class SetPointsSubCommand
 * @package juqn\hcf\faction\command\subcommand\admin
 */
class SetPointsSubCommand implements FactionSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender->hasPermission('setpoints.permission')) {
            return;
        }
        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::colorize('&cUse /faction setpoints [string: name] [int: points]'));
            return;
        }
        if (!is_numeric($args[1])) {
            $sender->sendMessage(TextFormat::colorize('&cUse /faction setpoints [string: name] [int: points]'));
            return;
        }

        $name = $args[0];
        $points = $args[1];

        if (HCFLoader::getInstance()->getFactionManager()->getFaction($name) === null) {
            $sender->sendMessage(TextFormat::colorize('&cThere is no faction you\'re trying to change the dtr'));
            return;
        }
        HCFLoader::getInstance()->getFactionManager()->getFaction($name)->setPoints($points * 1);
        $sender->sendMessage(TextFormat::colorize('&aThe Points of the faction ' . $name . ' is now ' . $points));
    }
}
