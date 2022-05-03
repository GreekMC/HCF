<?php

declare(strict_types=1);

namespace juqn\hcf\crate\command\subcommand;

use juqn\hcf\crate\command\CrateSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class DeleteSubCommand
 * @package juqn\hcf\crate\command\subcommand
 */
class DeleteSubCommand implements CrateSubCommand
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
            $sender->sendMessage(TextFormat::colorize('&c/crate delete [string: crateName]'));
            return;
        }
        $crateName = $args[1];
        
        if (HCFLoader::getInstance()->getCrateManager()->getCrate($crateName) === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis crate does not exist'));
            return;
        }
        HCFLoader::getInstance()->getCrateManager()->removeCrate($crateName);
        $sender->sendMessage(TextFormat::colorize('&cYou have removed the crate ' . $crateName . '. Now remove the chests that have been created with this crate'));
    }
}