<?php

declare(strict_types=1);

namespace juqn\hcf\shop;

use pocketmine\item\Item;

/**
 * Class Shop
 * @package juqn\hcf\shop
 */
class Shop
{
    
    /** @var int */
    const TYPE_BUY = 0;
    /** @var int */
    const TYPE_SELL = 1;
    
    /**
     * Shop construct.
     * @param int $type
     * @param int $price
     * @param Item $item
     */
    public function __construct(
        private int $type,
        private int $price,
        private Item $item
    ) {}
    
    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }
    
    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }
    
    /**
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }
    
    /**
     * @return array
     */
    public function getData(): array
    {
        return [
            'type' => $this->type,
            'price' => $this->price,
            'item' => $this->item->jsonSerialize()
        ];
    }
}