<?php

declare(strict_types=1);

namespace juqn\hcf\koth\command\subcommand;

use juqn\hcf\koth\command\KothSubCommand;
use juqn\hcf\koth\KothCapzone;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\utils\TextFormat;

/**
 * Class SetCapzoneSubCommand
 * @package juqn\hcf\koth\command\subcommand
 */
class SetCapzoneSubCommand implements KothSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if (count($args) < 1) {
            if (($creator = HCFLoader::getInstance()->getClaimManager()->getCreator($sender->getName())) !== null && $creator->getType() === 'capzone') {
                if (!$creator->isValid()) {
                    $sender->sendMessage(TextFormat::colorize('&cYou have not selected the capzone'));
                    return;
                }
                $koth = HCFLoader::getInstance()->getKothManager()->getKoth($creator->getName());
                $koth->setCapzone(new KothCapzone($creator->getMinX(), $creator->getMaxX(), $creator->getMinY(), $creator->getMaxY(), $creator->getMinZ(), $creator->getMaxZ(), $creator->getWorld()));
                $sender->sendMessage(TextFormat::colorize('&aYou have made the capzone of the koth ' . $creator->getName()));
                
                foreach ($sender->getInventory()->getContents() as $slot => $i) {
                    if ($i->getId() === 294 && $i->getNamedTag()->getTag('claim_type')) {
                        $sender->getInventory()->clear($slot);
                        break;
                    }
                }
                return;
            }
            $sender->sendMessage(TextFormat::colorize('&c/koth setcapzone [string: name]'));
            return;
        }
        
        if ($args[0] === 'cancel') {
            if (($creator = HCFLoader::getInstance()->getClaimManager()->getCreator($sender->getName())) !== null && $creator->getType() === 'capzone') {
                HCFLoader::getInstance()->getClaimManager()->removeCreator($sender->getName());
                $sender->sendMessage(TextFormat::colorize('&cYou have canceled the claim capzone'));
            } else
                $sender->sendMessage(TextFormat::colorize('&cYou are not in claim capzone mode yet'));
            return;
        }
        $name = $args[0];
        
        if (HCFLoader::getInstance()->getClaimManager()->getCreator($sender->getName()) !== null) {
            $sender->sendMessage(TextFormat::colorize('&cYou are already creating a capzone'));
            return;
        }
        
        if (HCFLoader::getInstance()->getKothManager()->getKoth($name) === null) {
            $sender->sendMessage(TextFormat::colorize('&cThe koth does not exist'));
            return;
        }
        $item = ItemFactory::getInstance()->get(294)->setCustomName(TextFormat::colorize('&eClaim capzone selector'));
        $item->setNamedTag($item->getNamedTag()->setString('claim_type', 'capzone'));
        
        if (!$sender->getInventory()->canAddItem($item)) {
            $sender->sendMessage(TextFormat::colorize('&cYou cannot add the item to make the claim capzone to your inventory'));
            return;
        }
        $sender->getInventory()->addItem($item);
        HCFLoader::getInstance()->getClaimManager()->createCreator($sender->getName(), $name, 'capzone');
        $sender->sendMessage(TextFormat::colorize('&aNow you can claim the capzone'));
    }
}