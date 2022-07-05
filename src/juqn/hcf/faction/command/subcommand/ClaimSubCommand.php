<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\utils\TextFormat;

class ClaimSubCommand implements FactionSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if ($sender->getSession()->getFaction() === null) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have a faction'));
            return;
        }
        $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($sender->getSession()->getFaction());
        
        if ($faction->getRole($sender->getXuid()) !== 'leader') {
            $sender->sendMessage(TextFormat::colorize('&cYou aren\'t the leader of the faction to claim'));
            return;
        }
        
        if (count($args) < 1) {
            if (($creator = HCFLoader::getInstance()->getClaimManager()->getCreator($sender->getName())) !== null && $creator->getType() === 'faction') {
                if (!$creator->isValid()) {
                    $sender->sendMessage(TextFormat::colorize('&cYou have not selected the claim'));
                    return;
                }
                $balance = $faction->getBalance() - $creator->calculateValue();
            
                if ($balance < 0) {
                    $sender->sendMessage(TextFormat::colorize('&cYour faction does not have enough money to pay the claim'));
                    return;
                }
                $creator->deleteCorners($sender);
                HCFLoader::getInstance()->getClaimManager()->createClaim($creator->getName(), $creator->getType(), $creator->getMinX(), $creator->getMaxX(), $creator->getMinZ(), $creator->getMaxZ(), $creator->getWorld());
                $sender->sendMessage(TextFormat::colorize('&aYou have successfully claimed'));
                HCFLoader::getInstance()->getClaimManager()->removeCreator($sender->getName());
            
                foreach ($sender->getInventory()->getContents() as $slot => $i) {
                    if ($i->getId() === 294 && $i->getNamedTag()->getTag('claim_type')) {
                        $sender->getInventory()->clear($slot);
                        break;
                    }
                }
                return;
            }
        }
        
        if (count($args) !== 0) {
            if ($args[0] === 'cancel') {
                if (($creator = HCFLoader::getInstance()->getClaimManager()->getCreator($sender->getName())) !== null && $creator->getType() === 'faction') {
                    $creator->deleteCorners($sender);
                    HCFLoader::getInstance()->getClaimManager()->removeCreator($sender->getName());
                    $sender->sendMessage(TextFormat::colorize('&cYou have canceled the claim'));
                } else
                    $sender->sendMessage(TextFormat::colorize('&cYou are not in claim mode yet'));
                return;
            }
        }
        
        if (HCFLoader::getInstance()->getClaimManager()->getCreator($sender->getName()) !== null) {
            $sender->sendMessage(TextFormat::colorize('&cYou are already creating a claim'));
            return;
        }
        
        if (HCFLoader::getInstance()->getClaimManager()->getCreateByClaimName($faction->getName())) {
            $sender->sendMessage(TextFormat::colorize('&cSomeone from your faction is already claim'));
            return;
        }
        
        if (HCFLoader::getInstance()->getClaimManager()->getClaim($faction->getName()) !== null) {
            $sender->sendMessage(TextFormat::colorize('&cYour faction already has a claim'));
            return;
        }
        $item = ItemFactory::getInstance()->get(294)->setCustomName(TextFormat::colorize('&eClaim selector'));
        $item->setNamedTag($item->getNamedTag()->setString('claim_type', 'faction'));
        
        if (!$sender->getInventory()->canAddItem($item)) {
            $sender->sendMessage(TextFormat::colorize('&cYou cannot add the item to make the claim to your inventory'));
            return;
        }
        $sender->getInventory()->addItem($item);
        HCFLoader::getInstance()->getClaimManager()->createCreator($sender->getName(), $faction->getName(), 'faction');
        $sender->sendMessage(TextFormat::colorize('&aNow you can claim the area'));
    }
}
