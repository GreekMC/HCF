<?php

declare(strict_types=1);

namespace juqn\hcf\crate\command\subcommand;

use juqn\hcf\crate\command\CrateSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\utils\TextFormat;

/**
 * Class ItemConfigurationSubCommand
 * @package juqn\hcf\crate\command\subcommand
 */
class ItemConfigurationSubCommand implements CrateSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        $item = ItemFactory::getInstance()->get(286, 0);
        $item->setCustomName(TextFormat::colorize('&dCrate Configuration'));
        $item->setNamedTag($item->getNamedTag()->setString('crate_configuration', 'true'));
        
        $sender->getInventory()->addItem($item);
    }
}