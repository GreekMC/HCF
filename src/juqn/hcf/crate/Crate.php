<?php

declare(strict_types=1);

namespace juqn\hcf\crate;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

/**
 * Class Crate
 * @package juqn\hcf
 */
class Crate
{
    
    /** @var string */
    private string $name;
    /** @var string */
    private string $keyId;
    /** @var string */
	private string $keyFormat;
	/** @var string */
	private string $nameFormat;
	/** @var Item[] */
    private array $items;
    
    /**
     * Crate construct.
     * @param string $name
     * @param string $keyId
     * @param string $keyFormat
     * @param string $nameFormat
     * @param Item[] $items
     */
    public function __construct(string $name, string $keyId, string $keyFormat, string $nameFormat, array $items)
    {
        $this->name = $name;
        $this->keyId = $keyId;
        $this->keyFormat = $keyFormat;
        $this->nameFormat = $nameFormat;
        $this->items = $items;
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
	public function getKeyId(): string
	{
		return $this->keyId;
	}
    
    /**
	 * @return string
	 */
	public function getKeyFormat(): string
	{
		return $this->keyFormat;
	}
	
	/**
	 * @return string
	 */
	public function getNameFormat(): string
	{
		return $this->nameFormat;
	}
	
	/**
	 * @return Item[]
	 */
	public function getItems(): array
    {
        return $this->items;
    }
    
    /**
	 * @param string $keyId
	 */
	public function setKeyId(string $keyId): void
	{
		$this->keyId = $keyId;
	}
	
	/**
	 * @param string $keyFormat
	 */
	public function setKeyFormat(string $keyFormat): void
	{
		$this->keyFormat = $keyFormat;
	}
	
	/**
	 * @param string $nameFormat
	 */
	public function setNameFormat(string $nameFormat): void
	{
		$this->nameFormat = $nameFormat;
	}
	
	/**
	 * @param Item[] $items
	 */
	public function setItems(array $items): void
    {
        $this->items = $items;
    }
    
    /**
     * @param Player $player
     * @param int $count
     * @return bool
     */
    public function giveKey(Player $player, int $count = 1): bool
    {
        $id = explode(':', $this->getKeyId());
        $itemMeta = isset($id[1]) ? (int) $id[1] : 0;
        
        $item = ItemFactory::getInstance()->get((int) $id[0], $itemMeta, $count);
        $item->setCustomName(TextFormat::colorize($this->getKeyFormat()));
        $item->setLore([
            TextFormat::GRAY . 'You can redeem this key at crate',
			TextFormat::GRAY . 'in the spawn area.',
			'',
			TextFormat::GRAY . TextFormat::ITALIC . 'Left click to view crate rewards.',
			TextFormat::GRAY . TextFormat::ITALIC . 'Right click to open the crate.',
			'',
			TextFormat::RESET . TextFormat::YELLOW . 'Available for purchase at store.greekmc.net'
        ]);
        $item->setNamedTag($item->getNamedTag()->setString('crate_name', $this->getName()));
        
        if (!$player->getInventory()->canAddItem($item))
            return false;
        $player->getInventory()->addItem($item);
        return true;
    }
    
    /**
     * @param Player $player
     * @return bool
     */
    public function giveReward(Player $player): bool
    {
        $items = $this->getItems();
        $randomItem = $items[array_rand($items)];
        
        if (!$player->getInventory()->canAddItem($randomItem))
            return false;
        $itemInHand = $player->getInventory()->getItemInHand();
        $itemInHand->pop();
        $player->getInventory()->setItemInHand($itemInHand->isNull() ? ItemFactory::air() : $itemInHand);
        $player->getInventory()->addItem($randomItem);
        return true;
    }
    
    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [
            'keyId' => $this->getKeyId(),
            'keyFormat' => $this->getKeyFormat(),
            'nameFormat' => $this->getNameFormat()
        ];
        
        foreach ($this->getItems() as $slot => $item)
            $data['items'][$slot] = $item->jsonSerialize();
        return $data;
    }
}