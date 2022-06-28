<?php

declare(strict_types=1);

namespace juqn\hcf\kit;

use pocketmine\item\Item;
use pocketmine\player\Player;

/**
 * Class Kit
 * @package juqn\hcf\kit
 */
class Kit
{
    
    /** @var string */
    private string $name;
    /** @var string */
    private string $nameFormat;
    /** @var string|null */
    private ?string $permission;
    
    /** @var Item[] */
    private array $items, $armor;
    
    /** @var int */
    private int $cooldown;
    
    /** @var Item|null */
    private ?Item $representativeItem;

    /**
     * Kit construct.
     * @param string $name
     * @param string $nameFormat
     * @param string|null $permission
     * @param Item|null $representativeItem
     * @param Item[] $items
     * @param Item[] $armor
     * @param int $cooldown
     */
    public function __construct(string $name, string $nameFormat, ?string $permission, ?Item $representativeItem, array $items, array $armor, int $cooldown)
    {
        $this->name = $name;
        $this->nameFormat = $nameFormat;
        $this->permission = $permission;
        $this->representativeItem = $representativeItem;
        $this->items = $items;
        $this->armor = $armor;
        $this->cooldown = $cooldown;
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @return string
     */
    public function getNameFormat(): string
    {
        return $this->nameFormat;
    }
    
    /**
     * @return string|null
     */
    public function getPermission(): ?string
    {
        return $this->permission;
    }
    
    /**
     * @return Item|null
     */
    public function getRepresentativeItem(): ?Item
    {
        return $this->representativeItem;
    }
    
    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
    
    /**
     * @return Item[]
     */
    public function getArmor(): array
    {
        return $this->armor;
    }
    
    /**
     * @return int
     */
    public function getCooldown(): int
    {
        return $this->cooldown;
    }
    
    /**
     * @param Item|null $item
     */
    public function setRepresentativeItem(?Item $item): void
    {
        $this->representativeItem = $item;
    }
    
    /**
     * @param Item[] $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }
    
    /**
     * @param Item[] $armor
     */
    public function setArmor(array $armor): void
    {
        $this->armor = $armor;
    }
    
    /**
     * @param Player $player
     */
    public function giveTo(Player $player): void
    {
        foreach ($this->getItems() as $slot => $item) {
            if ($player->getInventory()->canAddItem($item))
                $player->getInventory()->addItem($item);
            else
                $player->getWorld()->dropItem($player->getPosition()->asVector3(), $item);
        }
        
        for ($i = 0; $i < 4; $i++) {
            if (isset($this->armor[$i])) {
                if ($player->getArmorInventory()->getItem($i)->isNull())
                    $player->getArmorInventory()->setItem($i, $this->armor[$i]);
                else {
                    if ($player->getInventory()->canAddItem($this->armor[$i]))
                        $player->getInventory()->addItem($this->armor[$i]);
                    else
                        $player->dropItem($this->armor[$i]);
                }
            }
        }
    }
    
    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [
            'nameFormat' => $this->getNameFormat(),
            'permission' => $this->getPermission(),
            'cooldown' => $this->getCooldown(),
            'representativeItem' => $this->getRepresentativeItem()?->jsonSerialize(),
            'items' => [],
            'armor' => []
        ];
        
        foreach ($this->getItems() as $slot => $item)
            $data['items'][$slot] = $item->jsonSerialize();
            
        foreach ($this->getArmor() as $slot => $armor)
            $data['armor'][$slot] = $armor->jsonSerialize();
        return $data;
    }
}