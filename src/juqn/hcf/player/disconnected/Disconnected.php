<?php

declare(strict_types=1);

namespace juqn\hcf\player\disconnected;

use juqn\hcf\HCFLoader;
use juqn\hcf\session\Session;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class Disconnected
{
    
    /**
     * Disconnected construct.
     * @param string $xuid
     * @param string $name
     * @param float $health
     * @param Location $location
     * @param Item[] $inventory
     * @param Item[] $armorInventory
     * @param DisconnectedMob|null $disconnectedMob
     */
    public function __construct(
        private string $xuid,
        private string $name,
        private float $health,
        private Location $location,
        private array $inventory,
        private array $armorInventory,
        private ?DisconnectedMob $disconnectedMob = null
    ) {
        $this->spawn();
    }
    
    public function spawn(): void
    {
        $this->disconnectedMob = new DisconnectedMob($this->getLocation());
        $this->disconnectedMob->setCanSaveWithChunk(true);
        $this->disconnectedMob->setDisconnected($this);
        $this->disconnectedMob->setHealth($this->getHealth());
        $this->disconnectedMob->setNameTagVisible();
        $this->disconnectedMob->setNameTagAlwaysVisible(true);
        $this->disconnectedMob->setNameTag(TextFormat::colorize('&7(Combat-Logger)&c ' . $this->getName()));
        $this->disconnectedMob->spawnToAll();
    }
    
    /**
     * @return Session|null
     */
    public function getSession(): ?Session
    {
        return HCFLoader::getInstance()->getSessionManager()->getSession($this->xuid);
    }
    
    /**
     * @return string
     */
    public function getXuid(): string
    {
        return $this->xuid;
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @return float
     */
    public function getHealth(): float
    {
        return $this->health;
    }
    
    /**
     * @return Location
     */
    public function getLocation(): Location
    {
        return $this->location;
    }
    
    /**
     * @return Item[]
     */
    public function getInventory(): array
    {
        return $this->inventory;
    }
    
    /**
     * @return Item[]
     */
    public function getArmorInventory(): array
    {
        return $this->armorInventory;
    }
    
    /**
     * @return DisconnectedMob|null
     */
    public function getDisconnectedMob(): ?DisconnectedMob
    {
        return $this->disconnectedMob;
    }
    
    /**
     * @param Player $player
     */
    public function join(Player $player): void
    {
        $mob = $this->getDisconnectedMob();
        
        if ($mob !== null && !$mob->isClosed()) {
            $player->teleport($mob->getLocation());
            $player->setHealth($mob->getHealth());
            
            $mob->flagForDespawn();
        } else {
            $player->teleport($this->getLocation());
        }
        HCFLoader::getInstance()->getDisconnectedManager()->removeDisconnected($player->getXuid());
    }
}