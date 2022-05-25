<?php

declare(strict_types=1);

namespace juqn\hcf\crate\command\subcommand;

use juqn\hcf\crate\command\CrateSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\utils\TextFormat;

class GiveSubCommand extends CrateSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
            
        if (count($args) === 0) {
            $sender->sendMessage(TextFormat::colorize('&cUse /crate give [string: crateName]'));
            return;
        }
        $crateName = $args[0];
        $crate = HCFLoader::getInstance()->getCrateManager()->getCrate($crateName);
        
        if ($crate === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis crate does not exist'));
            return;
        }
        $chest = ItemFactory::getInstance()->get(54, 0);
        $chest->setCustomName(TextFormat::colorize('Crate ' . $crateName));
        
        $namedtag = $chest->getNamedTag();
        $namedtag->setString('crate_place', $crateName);
        $chest->setNamedTag($namedtag);
            
        $sender->sendMessage(TextFormat::colorize('&aCrate ' . $crateName . ' given'));
        $sender->getInventory()->addItem($chest);
    }
}