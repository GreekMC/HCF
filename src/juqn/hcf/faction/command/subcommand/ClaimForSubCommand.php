<?php

declare(strict_types=1);

namespace juqn\hcf\faction\command\subcommand;

use juqn\hcf\faction\command\FactionSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\utils\TextFormat;

/**
 * Class ClaimForSubCommand
 * @package juqn\hcf\faction\command\subcommand
 */
class ClaimForSubCommand implements FactionSubCommand
{
    
    /** @var string[] */
    private array $claims = [
        'Spawn' => 'spawn',
        'North Road' => 'road',
        'South Road' => 'road',
        'West Road' => 'road',
        'East Road' => 'road',
        '§5Citadel§c' => 'citadel'
    ];
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
            
        if (!$sender->hasPermission('faction.command.claimfor')) {
            $sender->sendMessage(TextFormat::colorize('&cThis sub command does not exist'));
            return;
        }
        
        if (count($args) < 1) {
            if (($creator = HCFLoader::getInstance()->getClaimManager()->getCreator($sender->getName())) !== null && $creator->getType() === $this->claims[$creator->getName()]) {
                if (!$creator->isValid()) {
                    $sender->sendMessage(TextFormat::colorize('&cYou have not selected the claim'));
                    return;
                }
                HCFLoader::getInstance()->getClaimManager()->createClaim($creator->getName(), $creator->getType(), $creator->getMinX(), $creator->getMaxX(), $creator->getMinZ(), $creator->getMaxZ(), $creator->getWorld());
                $sender->sendMessage(TextFormat::colorize('&aYou have made the claim of the opclaim ' . $creator->getName()));
                HCFLoader::getInstance()->getClaimManager()->removeCreator($sender->getName());
            
                foreach ($sender->getInventory()->getContents() as $slot => $i) {
                    if ($i->getId() === 294 && $i->getNamedTag()->getTag('claim_type')) {
                        $sender->getInventory()->clear($slot);
                        break;
                    }
                }
                return;
            }

            $sender->sendMessage(TextFormat::colorize('&cUse /faction claimfor [string: name]'));
            return;
        }
        $claimName = implode(' ', $args);
        
        if ($claimName === 'cancel') {
            if (($creator = HCFLoader::getInstance()->getClaimManager()->getCreator($sender->getName())) !== null && $creator->getType() === $this->claims[$creator->getName()]) {
                HCFLoader::getInstance()->getClaimManager()->removeCreator($sender->getName());
                $sender->sendMessage(TextFormat::colorize('&cYou have canceled the claim'));
            } else
                $sender->sendMessage(TextFormat::colorize('&cYou are not in claim mode yet'));
            return;
        }
        
        if (HCFLoader::getInstance()->getClaimManager()->getCreator($sender->getName()) !== null) {
            $sender->sendMessage(TextFormat::colorize('&cYou are already creating a claim'));
            return;
        }
        
        if (!isset($this->claims[$claimName])) {
            $sender->sendMessage(TextFormat::colorize('&cInvalid claim name'));
            return;
        }
        $claimType = $this->claims[$claimName];
        
        if (HCFLoader::getInstance()->getFactionManager()->getFaction($claimName) === null)
            HCFLoader::getInstance()->getFactionManager()->createFaction($claimName, [
               'roles' => [],
               'dtr' => 1.01,
               'balance' => 0,
               'points' => 0,
               'kothCaptures' => 0,
               'timeRegeneration' => null,
               'home' => null,
               'claim' => null
           ]);
        $item = ItemFactory::getInstance()->get(294)->setCustomName(TextFormat::colorize('&eClaim selector'));
        $item->setNamedTag($item->getNamedTag()->setString('claim_type', $claimType));
        
        if (!$sender->getInventory()->canAddItem($item)) {
            $sender->sendMessage(TextFormat::colorize('&cYou cannot add the item to make the claim to your inventory'));
            return;
        }
        $sender->getInventory()->addItem($item);
        HCFLoader::getInstance()->getClaimManager()->createCreator($sender->getName(), $claimName, $claimType);
        $sender->sendMessage(TextFormat::colorize('&aNow you can claim the area'));
    }
}