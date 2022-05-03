<?php

declare(strict_types=1);

namespace juqn\hcf\enchantment\command\subcommand;

use juqn\hcf\enchantment\command\EnchantmentSubCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\utils\TextFormat;

/**
 * Class RemoveSubCommand
 * @package juqn\hcf\enchantment\command\subcommand
 */
class RemoveSubCommand implements EnchantmentSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
            
        if (!is_numeric($args[0])) {
            $sender->sendMessage(TextFormat::colorize('&cInvalid number'));
            return;
        }
        $enchantId = (int) $args[0];
        
        if (($enchantment = EnchantmentIdMap::getInstance()->fromId($enchantId)) === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis enchantment does not exist'));
            return;
        }
        $item = clone $sender->getInventory()->getItemInHand();
        
        if ($item->isNull()) {
            $sender->sendMessage(TextFormat::colorize('&cThis item cannot be enchanted'));
            return;
        }
        
        if (!$item->hasEnchantment($enchantment)) {
            $sender->sendMessage(TextFormat::colorize('&cThis item does not have this enchantment'));
            return;
        }
        $lore = $item->getLore();
        
        if (($key = array_search(TextFormat::colorize('&4' . $enchantment->getName() . ' ' . $enchantment->getMaxLevel()), $lore)) !== false)
            unset($lore[$key]);
        
        if (count($lore) === 1) $lore = [];
        $item->setLore($lore);
        $item->removeEnchantment($enchantment);
        $sender->getInventory()->setItemInHand($item);
        $sender->sendMessage(TextFormat::colorize('&aYou have successfully removed the enchantment'));
    }
}