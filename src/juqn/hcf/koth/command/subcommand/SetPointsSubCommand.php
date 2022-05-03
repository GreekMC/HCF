<?php

declare(strict_types=1);

namespace juqn\hcf\koth\command\subcommand;

use juqn\hcf\koth\command\KothSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class SetPointsSubCommand
 * @package juqn\hcf\koth\command\subcommand
 */
class SetPointsSubCommand implements KothSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::colorize('&c/koth setpoints [string: name] [int: points]'));
            return;
        }
        $name = $args[0];
        $points = $args[1];
        
        if (HCFLoader::getInstance()->getKothManager()->getKoth($name) === null) {
            $sender->sendMessage(TextFormat::colorize('&cThe koth does not exist'));
            return;
        }
        $koth = HCFLoader::getInstance()->getKothManager()->getKoth($name);
        
        if (!is_numeric($points)) {
            $sender->sendMessage(TextFormat::colorize('&cInvalid numbers'));
            return;
        }
        $koth->setPoints((int) $points);
        $sender->sendMessage(TextFormat::colorize('&aYou changed the points to ' . $points . ' of the koth ' . $name));
    }
}