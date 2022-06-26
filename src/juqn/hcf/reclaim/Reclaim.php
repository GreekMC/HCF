<?php

declare(strict_types=1);

namespace juqn\hcf\reclaim;

use juqn\hcf\player\Player;
use pocketmine\item\Item;

class Reclaim
{
    
    /**
     * Reclaim construct.
     * @param string $name
     * @param string $permission
     * @param int $time
     * @param Item[] $contents
     */
    public function __construct(
        private string $name,
        private string $permission,
        private int $time,
        private array $contents = []
    ) {}
    
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
    public function getPermission(): string
    {
        return $this->permission;
    }
    
    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }
    
    /**
     * @return Item[]
     */
    public function getContents(): array
    {
        return $this->contents;
    }
    
    /**
     * @param Item[] $contents
     */
    public function setContents(array $contents): void
    {
        $this->contents = $contents;
    }
    
    /**
     * @param Player $player
     */
    public function giveContent(Player $player): void
    {
        foreach ($this->getContents() as $item) {
            if ($player->getInventory()->canAddItem($item)) {
                $player->getInventory()->addItem($item);
            } else {
                $player->dropItem($item);
            }
        }
    }
    
    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [
            'permission' => $this->permission,
            'time' => $this->time,
            'contents' => []
        ];
        
        foreach ($this->contents as $item)
            $data['contents'][] = $item->jsonSerialize();
        return $data;
    }
}