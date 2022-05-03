<?php

declare(strict_types=1);

namespace juqn\hcf\shop;

use juqn\hcf\HCFLoader;

use pocketmine\item\Item;
use pocketmine\world\Position;

/**
 * Class ShopManager
 * @package juqn\hcf\shop
 */
class ShopManager
{
    
    /** @var Shop[] */
    private array $shops = [];
    
    /**
     * ShopManager construct.
     */
    public function __construct()
    {
        # Register shops
        foreach (HCFLoader::getInstance()->getProvider()->getShops() as $location => $data)
            $this->createShop($location, (int) $data['type'], (int) $data['price'], $data['item']);
        # Register listener
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new ShopListener(), HCFLoader::getInstance());
    }
    
    /**
     * @return Shop[]
     */
    public function getShops(): array
    {
        return $this->shops;
    }
    
    /**
     * @param Position $position
     * @return Shop|null
     */
    public function getShop(Position $position): ?Shop
    {
        return $this->shops[$position->__toString()] ?? null;
    }
    
    /**
     * @param Position|string $position
     * @param int $type
     * @param int $price
     * @param Item $item
     */
    public function createShop($position, int $type, int $price, Item $item): void
    {
        $position = $position instanceof Position ? $position->__toString() : $position;
        $this->shops[$position] = new Shop($type, $price, $item);
    }
    
    /**
     * @param Position|string $position
     */
    public function removeShop($position): void
    {
        $position = $position instanceof Position ? $position->__toString() : $position;
        unset($this->shops[$position]);
    }
}