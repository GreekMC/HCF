<?php

declare(strict_types=1);

namespace juqn\hcf\shop;

use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;
use pocketmine\block\BaseSign;
use pocketmine\block\utils\SignText;
use pocketmine\block\WallSign;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\ItemFactory;
use pocketmine\utils\TextFormat;

/**
 * Class ShopListener
 * @package juqn\hcf\shop
 */
class ShopListener implements Listener
{
    
    /**
     * @param SignChanceEvent $event
     */
    public function handleChange(SignChangeEvent $event): void
    {
        $player = $event->getPlayer();
        $sign = $event->getNewText();
        $lines = $sign->getLines();
        
        if (strtolower($lines[0]) === 'buy' || strtolower($lines[0]) === 'sell') {
            if (!$player->hasPermission('shop.sign.create'))
                return;
            
            if ($lines[1] === '' && $lines[2] === '' && $lines[3] === '')
                return;
            $type = Shop::TYPE_BUY;
            
            if (strtolower($lines[0]) === 'sell')
                $type = Shop::TYPE_SELL;
            $count = $lines[1];
            
            if (!is_numeric($count)) return;

            $item = explode(':', $lines[2]);
            
            foreach ($item as $id) {
                if (!is_numeric($id)) return;
            }
            $meta = $item[1] ?? 0;
            $i = ItemFactory::getInstance()->get((int) $item[0], (int) $meta);
            $price = $lines[3];
            
            if (!is_numeric($price)) return;
            HCFLoader::getInstance()->getShopManager()->createShop($event->getSign()->getPosition(), $type, (int) $price, $i);
            $event->setNewText(SignText::fromBlob(TextFormat::colorize(($type === Shop::TYPE_BUY ? '&a- Buy- -' : '&c- Sell -') . PHP_EOL . '&0' . $count . PHP_EOL . '&0' . $i->getName() . PHP_EOL . '&0$' . $price)));
        }
    }
    
    /**
     * @param PlayerInteractEvent $event
     */
    public function handleInteract(PlayerInteractEvent $event): void
    {
        $block = $event->getBlock();
        /** @var Player */
        $player = $event->getPlayer();
        $shop = HCFLoader::getInstance()->getShopManager()->getShop($block->getPosition());
        
        if ($shop !== null) {
            if (!$block instanceof BaseSign || !$block instanceof WallSign) {
                HCFLoader::getInstance()->getShopManager()->removeShop($block->getPosition());
                return;
            }
            
            if ($player->isGod()) {
                $event->cancel();
                return;
            }
            
            if ($shop->getType() === Shop::TYPE_BUY) {
                
            } elseif ($shop->getType() === Shop::TYPE_SELL) {
                
            }
        }
    }
}