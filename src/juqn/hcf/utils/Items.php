<?php

declare(strict_types=1);

namespace juqn\hcf\utils;

use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\utils\TextFormat;

/**
 * Class Items
 * @package juqn\hcf\utils
 */
final class Items
{
    
    /** @var string[] */
    private static $kitLore = [
        '&7Choose this kit to play',
        '&7It will help you in your progress',
        '&r',
        '&eCooldown: &f{kit_cooldown}',
        '&eAvailable in: &f{player_cooldown}'
    ];
    
    /** @var string[] */
    private static $vKitLore = [
        '&7Choose this vkit',
        '&7and you will be the best',
        '&r',
        '&eCooldown: &f{vkit_cooldown}',
        '&eAvailable in: &f{player_cooldown}',
        '&eUnlocked for: &f{vkit_unlocked_cooldown}'
    ];
    
    /**
     * @param Player $player
     * @param Item $item
     * @param string $kitName
     * @return Item
     */
    public static function createItemKitOrganization(Player $player, Item $item, string $kitName): Item
    {
        $kit = HCFLoader::getInstance()->getKitManager()->getKit($kitName);
        
        $item->setCustomName(TextFormat::colorize($kit->getNameFormat()));
        $item->setLore(array_map(function (mixed $text) use ($player, $kit) {
            $player_cooldown = $player->getSession()->getCooldown('kit.' . $kit->getName()) !== null ? Timer::convert($player->getSession()->getCooldown('kit.' . $kit->getName())->getTime()) : 'N/A';
            $kit_cooldown = $kit->getCooldown() !== 0 ? Timer::convert($kit->getCooldown()) : 'N/A';
            $text = str_replace(['{player_cooldown}', '{kit_cooldown}'], [$player_cooldown, $kit_cooldown], $text);
            return TextFormat::colorize($text);
        }, self::$kitLore));
        
        $namedtag = $item->getNamedTag();
        $namedtag->setString('kit_name', $kitName);
        $item->setNamedTag($namedtag);
        
        return $item;
    }
    
    /**
     * @param Player $player
     * @param Item $item
     * @param string $vKitName
     * @return Item
     */
    public static function createItemvKitOrganization(Player $player, Item $item, string $vKitName): Item
    {
        $vkit = HCFLoader::getInstance()->getvKitManager()->getvKit($vKitName);
        
        $item->setCustomName(TextFormat::colorize('&r&l&6vKit Shard&r&7: &f' . $vkit->getName()));
        $item->setLore(array_map(function (mixed $text) use ($player, $vkit) {
            $player_cooldown = $player->getSession()->getCooldown('vkit.' . $vkit->getName()) !== null ? Timer::convert($player->getSession()->getCooldown('vkit.' . $vkit->getName())->getTime()) : 'N/A';
            $vkit_cooldown = Timer::convert(259200);
            $vkit_unlocked = $player->getSession()->getCooldown('vkit.unlocked.' . $vkit->getName()) !== null ? Timer::convert($player->getSession()->getCooldown('vkit.unlocked.' . $vkit->getName())->getTime()) : 'N/A';
            $text = str_replace(['{player_cooldown}', '{vkit_cooldown}', '{vkit_unlocked_cooldown}'], [$player_cooldown, $vkit_cooldown, $vkit_unlocked], $text);
            return TextFormat::colorize($text);
        }, self::$vKitLore));
        
        $namedtag = $item->getNamedTag();
        $namedtag->setString('vkit_name', $vKitName);
        $item->setNamedTag($namedtag);
        
        return $item;
    }
}