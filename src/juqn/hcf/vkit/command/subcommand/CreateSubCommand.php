<?php

declare(strict_types=1);

namespace juqn\hcf\vkit\command\subcommand;

use juqn\hcf\vkit\command\vKitSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\utils\TextFormat;

/**
 * Class CreateSubCommand
 * @package juqn\hcf\vkit\command\subcommand
 */
class CreateSubCommand implements vKitSubCommand
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
            $sender->sendMessage(TextFormat::colorize('&c/vkit create [string: vKitName]'));
            return;
        }
        $vKitName = $args[0];   
        $items = $sender->getInventory()->getContents();
        
        if (HCFLoader::getInstance()->getvKitManager()->getvKit($vKitName) !== null) {
            $sender->sendMessage(TextFormat::colorize('&cThis vkit already exists'));
            return;
        }
        HCFLoader::getInstance()->getvKitManager()->createvKit($vKitName, $items);
        $sender->sendMessage(TextFormat::colorize('&aYou have successfully created the ' . $vKitName . ' vkit'));
    }
}