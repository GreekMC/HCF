<?php

declare(strict_types=1);

namespace juqn\hcf\koth\command\subcommand;

use juqn\hcf\koth\command\KothSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class AddKeySubCommand
 * @package juqn\hcf\koth\command\subcommand
 */
class AddKeySubCommand implements KothSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if (count($args) < 3) {
            $sender->sendMessage(TextFormat::colorize('&c/koth addkey [string: name] [string: keyName] [int: count]'));
            return;
        }
        $name = $args[0];
        $keyName = $args[1];
        $count = $args[2];
        
        if (HCFLoader::getInstance()->getKothManager()->getKoth($name) === null) {
            $sender->sendMessage(TextFormat::colorize('&cThe koth does not exist'));
            return;
        }
        
        if (HCFLoader::getInstance()->getCrateManager()->getCrate($keyName) === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis key does not exist'));
            return;
        }
        $koth = HCFLoader::getInstance()->getKothManager()->getKoth($name);
        
        if (!is_numeric($count)) {
            $sender->sendMessage(TextFormat::colorize('&cInvalid numbers'));
            return;
        }
        $koth->setKey($keyName);
        $koth->setKeyCount((int)$count);
        $sender->sendMessage(TextFormat::colorize('&aYou have added the key ' . $keyName . ' x' . (int)$count . ' to the koth ' . $name));
    }
}