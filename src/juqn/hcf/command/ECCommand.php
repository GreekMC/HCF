<?php

declare(strict_types=1);

namespace juqn\hcf\command;

use juqn\hcf\player\Player as HCFPlayer;
use muqsit\invmenu\InvMenu;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class ECCommand extends Command
{
    
    /**
     * ECCommand construct.
     */
    public function __construct()
    {
        parent::__construct('ec', 'Command for Ender Chest');
    }
    
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof HCFPlayer)
            return;
        
        $menu = InvMenu::create(InvMenu::TYPE_HOPPER);
        if ($sender->hasPermission('enderchest.permission')) {
            $menu = InvMenu::create(InvMenu::TYPE_CHEST);
            /*menu->getInventory()->setContents($sender->getEnderInventory()->getContents());
            $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
                $player->getEnderInventory()->setContents($inventory->getContents());
            });*/
        }
        $menu->getInventory()->setContents($sender->getEnderInventory()->getContents());
        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
            $player->getEnderInventory()->setContents($inventory->getContents());
        });
        
        $menu->send($sender, TextFormat::colorize('&7Ender chest'));
    }
}