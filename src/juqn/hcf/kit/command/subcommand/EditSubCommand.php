<?php

declare(strict_types=1);

namespace juqn\hcf\kit\command\subcommand;

use juqn\hcf\kit\command\KitSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;
use juqn\hcf\utils\Inventories;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class EditSubCommand
 * @package juqn\hcf\kit\command\subcommand
 */
class EditSubCommand implements KitSubCommand
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
            Inventories::editKitOrganization($sender);
            return;
        }
        
        $kitName = $args[0];
        $kit = HCFLoader::getInstance()->getKitManager()->getKit($kitName);
        
        if ($kit === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis kit does not exist'));
            return;
        }
        $kit->setItems($sender->getInventory()->getContents());
        $kit->setArmor($sender->getArmorInventory()->getContents());
        $sender->sendMessage(TextFormat::colorize('&a You have successfully edited the ' . $kit->getName() . ' kit'));
    }
}