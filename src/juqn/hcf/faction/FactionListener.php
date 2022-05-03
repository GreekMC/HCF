<?php

declare(strict_types=1);

namespace juqn\hcf\faction;

use pocketmine\block\Door;
use pocketmine\block\FenceGate;
use pocketmine\block\tile\Sign;
use pocketmine\block\utils\SignText;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\World;

/**
 * Class FactionListener
 * @package juqn\hcf\faction
 */
class FactionListener implements Listener
{
    
    /**
     * @param SignChangeEvent $event
     */
    public function handleChange(SignChangeEvent $event): void
    {
        $signText = $event->getNewText();
        $lines = $signText->getLines();
        
        if (strtolower($lines[0]) === '[elevator]') {
            if (strtolower($lines[1]) === 'up')
                $event->setNewText(SignText::fromBlob(TextFormat::colorize('&e[Elevator]' . PHP_EOL . '&7up')));
            elseif (strtolower($lines[1]) === 'down')
                $event->setNewText(SignText::fromBlob(TextFormat::colorize('&e[Elevator]' . PHP_EOL . '&7down')));
        }
    }
    
    /**
     * @param PlayerInteractEvent $event
     */
    public function handleInteract(PlayerInteractEvent $event): void
    {
        $action = $event->getAction();
        $block = $event->getBlock();
        $player = $event->getPlayer();
        
        $tile = $player->getWorld()->getTile($block->getPosition());
        
        if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            if ($tile instanceof Sign) {
                $text = $tile->getText();
                $lines = $text->getLines();
            
                if ($lines[0] === TextFormat::colorize('&e[Elevator]')) {
                    if ($lines[1] === TextFormat::colorize('&7up')) {
                        for ($i = $block->getPosition()->getFloorY() + 1; $i < World::Y_MAX; $i++) {
                            $firstBlock = $player->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $i + 1, $block->getPosition()->getFloorZ());
                            $secondBlock = $player->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $i, $block->getPosition()->getFloorZ());
                            $thirdBlock = $player->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $i - 1, $block->getPosition()->getFloorZ());
                            
                            if (((($firstBlock instanceof FenceGate || $firstBlock instanceof Door) && $firstBlock->isOpen()) || !$firstBlock->isSolid()) &&
                                ((($secondBlock instanceof FenceGate || $secondBlock instanceof Door) && $secondBlock->isOpen()) || !$secondBlock->isSolid()) &&
                                $thirdBlock->isSolid()) {
                                $player->teleport(new Position($block->getPosition()->getFloorX() + 0.5, $i, $block->getPosition()->getFloorZ() + 0.5, $player->getWorld()));
                                break;
                            }
                        }
                    } elseif ($lines[1] === TextFormat::colorize('&7down')) {
                        for ($i = $block->getPosition()->getFloorY() - 1; $i >= World::Y_MIN; $i--) {
                            $firstBlock = $player->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $i + 1, $block->getPosition()->getFloorZ());
                            $secondBlock = $player->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $i, $block->getPosition()->getFloorZ());
                            $thirdBlock = $player->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $i - 1, $block->getPosition()->getFloorZ());
                        
                            if (((($firstBlock instanceof FenceGate || $firstBlock instanceof Door) && $firstBlock->isOpen()) || !$firstBlock->isSolid()) &&
                                ((($secondBlock instanceof FenceGate || $secondBlock instanceof Door) && $secondBlock->isOpen()) || !$secondBlock->isSolid()) &&
                                $thirdBlock->isSolid()) {
                                $player->teleport(new Position($block->getPosition()->getFloorX() + 0.5, $i, $block->getPosition()->getFloorZ() + 0.5, $player->getWorld()));
                                break;
                            }
                        }
                    }
                }
            }
        }
    }
}