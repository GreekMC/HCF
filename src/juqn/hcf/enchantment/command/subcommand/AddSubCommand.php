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
 * Class AddSubCommand
 * @package juqn\hcf\enchantment\command\subcommand
 */
class AddSubCommand implements EnchantmentSubCommand
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
            $sender->sendMessage(TextFormat::colorize('&c/customenchant add [int: enchantId]'));
            return;
        }
            
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
        
        if ($item->hasEnchantment($enchantment)) {
            $sender->sendMessage(TextFormat::colorize('&cThe item already contains this enchantment'));
            return;
        }
        $lore = $item->getLore();
        
        if (count($lore) === 0) {
            $lore = [TextFormat::colorize('&r'), TextFormat::colorize('&4' . $enchantment->getName() . ' ' . $enchantment->getMaxLevel())];
        } else {
            $lore[] = TextFormat::colorize('&4' . $enchantment->getName());
        }
        $item->setLore($lore);
        $item->addEnchantment(new EnchantmentInstance($enchantment, $enchantment->getMaxLevel()));
        $sender->getInventory()->setItemInHand($item);
        $sender->sendMessage(TextFormat::colorize('&aYou have successfully enchanted the item'));
    }
}