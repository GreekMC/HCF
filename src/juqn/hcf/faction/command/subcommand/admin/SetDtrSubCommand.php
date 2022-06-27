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

        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::colorize('&cUse /faction setdtr [string: name]'));
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

            if ($player->getName() === $sender->getName()) {
                $sender->sendMessage(TextFormat::colorize('&cYou cannot focus on yourself'));
                return;
            }

            if ($player->getSession()->getFaction() === $faction->getName()) {
                $sender->sendMessage(TextFormat::colorize('&cYou can\' focus on members of your faction'));
                return;
            }
            $factionName = $player->getSession()->getFaction();
        } else {
            if (HCFLoader::getInstance()->getFactionManager()->getFaction($name) === null) {
                $sender->sendMessage(TextFormat::colorize('&cThere is no faction you\'re trying to focus'));
                return;
            }

            if ($name === $sender->getSession()->getFaction()) {
                $sender->sendMessage(TextFormat::colorize('&cYou can\'t focus on your faction'));
                return;
            }
            $factionName = $name;
        }
        $faction->setFocus($factionName);
        $sender->sendMessage(TextFormat::colorize('&aNow your faction is targeting the faction ' . $factionName));
    }
}