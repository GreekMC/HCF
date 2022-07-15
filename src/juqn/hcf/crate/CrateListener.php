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
        $item = $player->getInventory()->getItemInHand();

        if ($block->getId() == BlockLegacyIds::CHEST) {
            $tile = $player->getWorld()->getTile($block->getPosition()->asVector3());

            if ($tile instanceof CrateTile) {
                $event->cancel();

                if ($player->getInventory()->getItemInHand()->getId() !== 286 && $player->getInventory()->getItemInHand()->getNamedTag()->getTag('crate_configuration') === null && !$player->hasPermission("god.command")) {
                    if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                        $tile->openCratePreview($player);
                    } elseif ($action === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
                        $tile->reedemKey($player);
                    }
                } else $tile->openCrateConfiguration($player);
                return;
            }

            if ($tile instanceof Chest) {
                if ($player->getInventory()->getItemInHand()->getId() === 286 && $player->getInventory()->getItemInHand()->getNamedTag()->getTag('crate_configuration') !== null ) {
                    $event->cancel();
                    Forms::createCreateTile($player, $block->getPosition());
                    return;
                }

                if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK && $item->getNamedTag()->getTag('crate_place') !== null) {
                    $crateName = $item->getNamedTag()->getString('crate_place');
                    $event->cancel();

                    if (HCFLoader::getInstance()->getCrateManager()->getCrate($crateName) === null) return;

                    $tilePosition = $block->getPosition()->asVector3();
                    $tile->close();

                    $newTile = new CrateTile($player->getWorld(), $tilePosition);
                    $newTile->setCrateName($crateName);
                    $player->getWorld()->addTile($newTile);

                    $player->sendMessage(TextFormat::colorize('&aYou have created the crate ' . $crateName . ' successfully'));
                }
            }
        }
    }
}