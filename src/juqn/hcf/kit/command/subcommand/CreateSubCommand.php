<?php

declare(strict_types=1);

namespace juqn\hcf\kit\command\subcommand;

use juqn\hcf\kit\command\KitSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\utils\TextFormat;

/**
 * Class CreateSubCommand
 * @package juqn\hcf\kit\command\subcommand
 */
class CreateSubCommand implements KitSubCommand
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
            $sender->sendMessage(TextFormat::colorize('&c/kit create [string: kitName] [string: nameFormat] [string: itemRepresentative] [int: cooldown | optional] [string: permission | optional]'));
            return;
        }
        $kitName = $args[0];
        $nameFormat = $args[1];
        $representativeItem = explode(':', $args[2]);
        $cooldown = (int) $args[3] ?? 0;
        $permission = $args[4];
        
        $items = $sender->getInventory()->getContents();
        $armor = $sender->getArmorInventory()->getContents();
        
        if (HCFLoader::getInstance()->getKitManager()->getKit($kitName) !== null) {
            $sender->sendMessage(TextFormat::colorize('&cThis kit already exists'));
            return;
        }
        
        if (count($representativeItem) > 2) {
            $sender->sendMessage(TextFormat::colorize('&cThe format of the representative item is invalid'));
            return;
        }
        
        if (!is_numeric($representativeItem[0]) || isset($representativeItem[1]) && !is_numeric($representativeItem[1])) {
            $sender->sendMessage(TextFormat::colorize('&cThe representative item has to be represented in numbers'));
            return;
        }
        $itemMeta = isset($representativeItem[1]) ? (int) $representativeItem[1] : 0;
        $representativeItem = ItemFactory::getInstance()->get((int) $representativeItem[0], $itemMeta);
        
        if (!is_numeric($cooldown)) {
            $sender->sendMessage(TextFormat::colorize('&cCooldown invalid'));
            return;
        }
        HCFLoader::getInstance()->getKitManager()->addKit($kitName, $nameFormat, $permission, $representativeItem, $items, $armor, $cooldown);
        $sender->sendMessage(TextFormat::colorize('&aYou have successfully created the ' . $kitName . ' kit'));
        
        HCFLoader::getInstance()->getKitManager()->registerPermission($permission);
        /*$permissionManager = PermissionManager::getInstance();
        if ($permission !== null && $permissionManager->getPermission($permission) !== null) {
            $permissionManager->addPermission(new Permission($permission, 'Permission for the reclaim ' . $kitName));
		    $permissionManager->getPermission(DefaultPermissions::ROOT_USER)->addChild($permission, true);
        }
    }*/
}