<?php

declare(strict_types=1);

namespace juqn\hcf\vkit\command\subcommand;

use juqn\hcf\vkit\command\vKitSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class GiveSubCommand
 * @package juqn\hcf\vkit\command\subcommand
 */
class GiveSubCommand implements vKitSubCommand
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
            $sender->sendMessage(TextFormat::colorize('&c/vkit give [string: vKitName] [int: count]'));
            return;
        }
        $vKitName = $args[0];
        $count = $args[1];
        
        if (HCFLoader::getInstance()->getvKitManager()->getvKit($vKitName) === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis vkit does not exist'));
            return;
        }
        
        if (!is_numeric($count)) {
            $sender->sendMessage(TextFormat::colorize('&cInvalid number'));
            return;
        }
        $vkit = HCFLoader::getInstance()->getvKitManager()->getvKit($vKitName);
        $vkit->givevKit($sender, (int) $count);
    }
}