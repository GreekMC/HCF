<?php

declare(strict_types=1);

namespace juqn\hcf\vkit\command\subcommand;

use juqn\hcf\vkit\command\vKitSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class DeleteSubCommand
 * @package juqn\hcf\vkit\command\subcommand
 */
class DeleteSubCommand implements vKitSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&c/vkit delete [string: vKitName]'));
            return;
        }
        $vKitName = $args[0];
        
        if (HCFLoader::getInstance()->getvKitManager()->getvKit($vKitName) === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis vkit does not exist'));
            return;
        }
        HCFLoader::getInstance()->getvKitManager()->removevKit($vKitName);
        $sender->sendMessage(TextFormat::colorize('&cYou have successfully removed vkit ' . $vKitName));
    }
}