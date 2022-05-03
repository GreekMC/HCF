<?php

declare(strict_types=1);

namespace juqn\hcf\command;

use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\item\Armor;
use pocketmine\item\Tool;
use pocketmine\utils\TextFormat;

/**
 * Class RenameCommand
 * @package juqn\hcf\command
 */
class RenameCommand extends Command
{
	
    /**
     * RenameCommand construct.
     */
    public function __construct()
    {
        parent::__construct('renam', 'Command for rename');
        $this->setPermission('rename.command');
    }
	
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&cUse /rename [name]'));
            return;
        }
        $item = clone $sender->getInventory()->getItemInHand();
        $name = implode(' ', $args);
        
        if (!$item instanceof Tool && !$item instanceof Armor) {
            $sender->sendMessage(TextFormat::colorize('&cYou have no armor or tools in your hand'));
            return;
        }
        $item->setCustomName(TextFormat::colorize($name));
        $sender->getInventory()->setItemInHand($item);
        $sender->sendMessage(TextFormat::colorize('&aYou have successfully renamed the item'));
    }
}