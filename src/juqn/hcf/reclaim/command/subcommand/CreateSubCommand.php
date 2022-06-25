<?php

declare(strict_types=1);

namespace juqn\hcf\reclaim\command\subcommand;

use juqn\hcf\HCFLoader;
use juqn\hcf\reclaim\command\ReclaimSubCommand;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\utils\TextFormat;

/**
 * Class CreateSubCommand
 * @package juqn\hcf\reclaim\command\subcommand
 */
class CreateSubCommand implements ReclaimSubCommand
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
            $sender->sendMessage(TextFormat::colorize('&cUse /reclaim create [name] [time] [permission]'));
            return;
        }
        $name = $args[0];
        $time = $args[1];
        $permission = $args[2];
        $contents = $sender->getInventory()->getContents();
        
        if (HCFLoader::getInstance()->getReclaimManager()->getReclaim($name) !== null) {
            $sender->sendMessage(TextFormat::colorize('&cThis reclaim already exists'));
            return;
        }
        
        if (!is_numeric($time)) {
            $sender->sendMessage(TextFormat::colorize('&cInvalid time'));
            return;
        }
        
        if (count($contents) === 0) {
            $sender->sendMessage(TextFormat::colorize('&cAssign contents to the reclaim. Put items in your inventory'));
            return;
        }
        HCFLoader::getInstance()->getReclaimManager()->createReclaim($name, $permission, (int) $time, $contents);
        $sender->sendMessage(TextFormat::colorize('&aYou have created the reclaim ' . $name . ' successfully'));
        
        $permissionManager = PermissionManager::getInstance();
        if ($permissionManager->getPermission($permission) !== null) {
            $permissionManager->addPermission(new Permission($permission, 'Permission for the reclaim ' . $name));
		    $permissionManager->getPermission(DefaultPermissions::ROOT_USER)->addChild($permission, true);
        }
    }
}