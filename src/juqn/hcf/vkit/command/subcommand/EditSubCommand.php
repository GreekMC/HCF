<?php

declare(strict_types=1);

namespace juqn\hcf\vkit\command\subcommand;

use juqn\hcf\vkit\command\vKitSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;
use juqn\hcf\utils\Inventories;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class EditSubCommand
 * @package juqn\hcf\vkit\command\subcommand
 */
class EditSubCommand implements vKitSubCommand
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
            Inventories::editvKitOrganization($sender);
            return;
        }
        
        $vKitName = $args[0];
        $vkit = HCFLoader::getInstance()->getvKitManager()->getvKit($vKitName);
        
        if ($vkit === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis vkit does not exist'));
            return;
        }
        $vkit->setItems($sender->getInventory()->getContents());
        $sender->sendMessage(TextFormat::colorize('&a You have successfully edited the ' . $vkit->getName() . ' vkit'));
    }
}