<?php

declare(strict_types=1);

namespace juqn\hcf\crate\command\subcommand;

use juqn\hcf\crate\command\CrateSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;
use juqn\hcf\utils\Inventories;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class CreateSubCommand
 * @package juqn\hcf\crate\command\subcommand
 */
class CreateSubCommand implements CrateSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if (count($args) < 4) {
            $sender->sendMessage(TextFormat::colorize('&c/crate create [string: crateName] [string: keyId] [string: keyFormat] [string: nameFormat]'));
            return;
        }
        $crateName = $args[0];
        $keyId = $args[1];
        $keyFormat = $args[2];
        $nameFormat = $args[3];
        
        $item = explode(':', $args[1]);
        
        if (!is_numeric($item[0]) || isset($item[1]) && !is_numeric($item[1])) {
            $sender->sendMessage(TextFormat::colorize('&cInvalid keyId data'));
            return;
        }
        
        if (HCFLoader::getInstance()->getCrateManager()->getCrate($crateName) !== null) {
            $sender->sendMessage(TextFormat::colorize('&cThis crate already exists'));
            return;
        }
        Inventories::createCrateContent($sender, [
            'crateName' => $crateName,
            'keyId' => $keyId,
            'keyFormat' => $keyFormat,
            'nameFormat' => $nameFormat
        ]);
    }
}