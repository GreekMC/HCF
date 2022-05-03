<?php

declare(strict_types=1);

namespace juqn\hcf\crate;

use juqn\hcf\crate\tile\CrateTile;
use juqn\hcf\HCFLoader;
use juqn\hcf\utils\Forms;

use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Chest;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\TextFormat;

/**
 * Class CrateListener
 * @package juqn\hcf\crate
 */
class CrateListener implements Listener
{
    
    /**
     * @param BlockBreakEvent $event
     */
    public function handleBreak(BlockBreakEvent $event): void
    {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        $tile = $player->getWorld()->getTile($block->getPosition()->asVector3());
        
        if ($tile instanceof CrateTile)
            $event->cancel();
    }
    
    /**
     * @param PlayerInteractEvent $event
     */
    public function handleInteract(PlayerInteractEvent $event): void
    {
        $action = $event->getAction();
        $block = $event->getBlock();
        $player = $event->getPlayer();
        
        if ($block->getId() == BlockLegacyIds::CHEST) {
            $tile = $player->getWorld()->getTile($block->getPosition()->asVector3());
            
            if ($tile instanceof CrateTile) {
                $event->cancel();
                
                if ($player->getInventory()->getItemInHand()->getId() !== 286 && $player->getInventory()->getItemInHand()->getNamedTag()->getTag('crate_configuration') === null) {
                    if ($action == PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                        $tile->openCratePreview($player);
                    } elseif ($action == PlayerInteractEvent::LEFT_CLICK_BLOCK) {
                        $tile->reedemKey($player);
                    }
                } else $tile->openCrateConfiguration($player);
                return;
            }
            
            if ($tile instanceof Chest) {
                if (($creator = HCFLoader::getInstance()->getCrateManager()->getCreator($player->getName())) !== null) {
                    $event->cancel();
                    
                    $tilePosition = $block->getPosition()->asVector3();
                    $tile->close();
                    
                    HCFLoader::getInstance()->getCrateManager()->removeCreator($player->getName());
                    HCFLoader::getInstance()->getCrateManager()->addCrate($creator['crateName'], $creator['keyId'], $creator['keyFormat'], $creator['nameFormat'], (array) $creator['items']);
                    
                    $newTile = new CrateTile($player->getWorld(), $tilePosition);
                    $newTile->setCrateName($creator['crateName']);
                    $player->getWorld()->addTile($newTile);
                    
                    $player->sendMessage(TextFormat::colorize('&aYou have created the crate ' . $creator['crateName'] . ' successfully'));
                }
                
                if ($player->getInventory()->getItemInHand()->getId() === 286 && $player->getInventory()->getItemInHand()->getNamedTag()->getTag('crate_configuration') !== null) {
                    $event->cancel();
                    
                    Forms::createCreateTile($player, $block->getPosition());
                }
                return;
            }
        }
    }
}