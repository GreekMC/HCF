<?php

declare(strict_types=1);

namespace juqn\hcf\reclaim;

use pocketmine\item\Item;

/**
 * Class Reclaim
 * @package juqn\hcf\reclaim
 */
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