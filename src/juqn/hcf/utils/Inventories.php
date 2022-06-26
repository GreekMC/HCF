<?php

declare(strict_types=1);

namespace juqn\hcf\utils;

use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;

use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\utils\TextFormat;

/**
 * Class Inventories
 * @package juqn\hcf\utils
 */
final class Inventories
{
    
    /**
     * @param Player $player
     * @param array $data
     */
    public static function createCrateContent(Player $player, array $data): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory) use ($data): void {
            $data['items'] = $inventory->getContents();
            HCFLoader::getInstance()->getCrateManager()->addCrate($data['crateName'], $data['keyId'], $data['keyFormat'], $data['nameFormat'], (array) $data['items']);
            
            $chest = ItemFactory::getInstance()->get(54, 0);
            $chest->setCustomName(TextFormat::colorize('Crate ' . $data['crateName']));
            $namedtag = $chest->getNamedTag();
            $namedtag->setString('crate_place', $data['crateName']);
            $chest->setNamedTag($namedtag);
            
            $player->getInventory()->addItem($chest);
            $player->sendMessage(TextFormat::colorize('&aYou have successfully created the crate ' . $data['crateName']));
        });
        $menu->send($player, TextFormat::colorize('&dCrate content'));
    }
    
    /**
     * @param Player $player
     * @param string $crateName
     */
    public static function editCrateContent(Player $player, string $crateName): void
    {
        $crate = HCFLoader::getInstance()->getCrateManager()->getCrate($crateName);

        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->getInventory()->setContents($crate?->getItems());
        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory) use ($crate): void {
            $crate?->setItems($inventory->getContents());
            $player->sendMessage(TextFormat::colorize('&aYou have edited the content'));
        });
        $menu->send($player, TextFormat::colorize('&bEdit crate'));
    }
    
    /**
     * @param Player $player
     */
    public static function editvKitOrganization(Player $player): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        
        foreach (HCFLoader::getInstance()->getvKitManager()->getOrganization() as $slot => $vKitName) {
            $vkit = HCFLoader::getInstance()->getvKitManager()->getvKit($vKitName);
            
            if ($vkit !== null) $menu->getInventory()->setItem($slot, Items::createItemvKitOrganization($player, ItemFactory::getInstance()->get(388), $vkit->getName()));
        }
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            /** @var Player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClickedWith();
            
            if (!$item->isNull() && $item->getNamedTag()->getTag('vkit_name') === null)
                return $transaction->discard();
            return $transaction->continue();
        });
        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
            $data = [];
            $contents = $inventory->getContents();
            
            foreach ($contents as $slot => $item) {
                $vkit = HCFLoader::getInstance()->getvKitManager()->getvKit($item->getNamedTag()->getString('vkit_name'));
                
                if ($vkit !== null) $data[$slot] = $vkit->getName();
            }
            HCFLoader::getInstance()->getvKitManager()->setOrganization($data);
        });
        $menu->send($player, TextFormat::colorize('&3Edit vKit organization'));
    }
    
    /**
     * @param Player $player
     */
    public static function createvKitOrganization(Player $player): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $organization = HCFLoader::getInstance()->getvKitManager()->getOrganization();
        
        for ($i = 0; $i < 54; $i++) {
            if (isset($organization[$i])) {
                $vkit = HCFLoader::getInstance()->getvKitManager()->getvKit($organization[$i]);
                
                if ($vkit !== null)
                    $menu->getInventory()->setItem($i, Items::createItemvKitOrganization($player, ItemFactory::getInstance()->get(388), $vkit->getName()));
                else $menu->getInventory()->setItem($i, ItemFactory::getInstance()->get(160, 8));
            } else
                $menu->getInventory()->setItem($i, ItemFactory::getInstance()->get(160, 8));
        }
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            /** @var Player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            
            if ($item->getNamedTag()->getTag('vkit_name') !== null) {
                $vkit = HCFLoader::getInstance()->getvKitManager()->getvKit($item->getNamedTag()->getString('vkit_name'));
                
                if ($vkit !== null) {
                    if ($player->getSession()->getCooldown('vkit.unlocked.' . $vkit->getName()) === null) {
                        $player->sendMessage(TextFormat::colorize('&cYou do not have this vKit Shard unlocked'));
                        return $transaction->discard();
                    }
                    
                    if ($player->getSession()->getCooldown('vkit.' . $vkit->getName()) !== null) {
                        $player->sendMessage(TextFormat::colorize('&cYou have vkit cooldown. Time remaining ' . Timer::convert($player->getSession()->getCooldown('vkit.' . $vkit->getName())->getTime())));
                        return $transaction->discard();
                    }
                    $vkit->givevKit($player);
                    $player->getSession()->addCooldown('vkit.' . $vkit->getName(), '', 259200, false, false);
                }
            }
            return $transaction->discard();
        });
        $menu->send($player, TextFormat::colorize('&6Greek vKits'));
    }
    
    /**
     * @param Player $player
     */
    public static function createKitOrganization(Player $player): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $organization = HCFLoader::getInstance()->getKitManager()->getOrganization();
        
        for ($i = 0; $i < 54; $i++) {
            if (isset($organization[$i])) {
                $kit = HCFLoader::getInstance()->getKitManager()->getKit($organization[$i]);
                
                if ($kit !== null)
                    $menu->getInventory()->setItem($i, Items::createItemKitOrganization($player, $kit->getRepresentativeItem(), $kit->getName()));
                else $menu->getInventory()->setItem($i, ItemFactory::getInstance()->get(160, 8));
            } else
                $menu->getInventory()->setItem($i, ItemFactory::getInstance()->get(160, 8));
        }
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            /** @var Player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            
            if ($item->getNamedTag()->getTag('kit_name') !== null) {
                $kit = HCFLoader::getInstance()->getKitManager()->getKit($item->getNamedTag()->getString('kit_name'));
                
                if ($kit !== null) {
                    
                    # Permission
                    if ($kit->getPermission() !== null && !$player->hasPermission($kit->getPermission())) {
                        $player->sendMessage(TextFormat::colorize('&cYou do not have permission to use the kit'));
                        return $transaction->discard();
                    }
                    
                    # Cooldown
                    if ($player->getSession()->getCooldown('kit.' . $kit->getName()) !== null) {
                        $player->sendMessage(TextFormat::colorize('&cYou have kit cooldown. Time remaining ' . Timer::convert($player->getSession()->getCooldown('kit.' . $kit->getName())->getTime())));
                        return $transaction->discard();
                    }

                    # Give kit
                    $kit->giveTo($player);
                    
                    # Add cooldown
                    if ($kit->getCooldown() !== 0)
                        $player->getSession()->addCooldown('kit.' . $kit->getName(), '', $kit->getCooldown(), false, false);
                    
                    $player->removeCurrentWindow();
                }
            }
            return $transaction->discard();
        });
        $menu->send($player, TextFormat::colorize('&6Greek Kits'));
    }
    
    /**
     * @param Player $player
     */
    public static function editKitOrganization(Player $player): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        
        foreach (HCFLoader::getInstance()->getKitManager()->getOrganization() as $slot => $kitName) {
            $kit = HCFLoader::getInstance()->getKitManager()->getKit($kitName);
            
            if ($kit !== null) $menu->getInventory()->setItem($slot, Items::createItemKitOrganization($player, $kit->getRepresentativeItem(), $kit->getName()));
        }
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            /** @var Player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClickedWith();
            
            if (!$item->isNull() && $item->getNamedTag()->getTag('kit_name') === null)
                return $transaction->discard();
            return $transaction->continue();
        });
        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
            $data = [];
            $contents = $inventory->getContents();
            
            foreach ($contents as $slot => $item) {
                $kit = HCFLoader::getInstance()->getKitManager()->getKit($item->getNamedTag()->getString('kit_name'));
                
                if ($kit !== null) $data[$slot] = $kit->getName();
            }
            HCFLoader::getInstance()->getKitManager()->setOrganization($data);
        });
        $menu->send($player, TextFormat::colorize('&6Edit kit organization'));
    }
}