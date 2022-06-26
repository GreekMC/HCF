<?php

declare(strict_types=1);

namespace juqn\hcf\player\disconnected;

use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;

class DisconnectedManager
{
    
    /**
     * DisconnectedManager construct.
     * @param Disconnected[] $disconnected
     */
    public function __construct(
        private array $disconnected = []
    ) {
        EntityFactory::getInstance()->register(DisconnectedMob::class, function(World $world, CompoundTag $nbt): DisconnectedMob {
			return new DisconnectedMob(EntityDataHelper::parseLocation($nbt, $world), $nbt);
		}, ['DisconnectedMob', 'hcf:disconnectedmob'], EntityLegacyIds::VILLAGER);
    }
    
    public function onDisable(): void
    {
        foreach (Server::getInstance()->getWorldManager()->getDefaultWorld()->getEntities() as $entity) {
            if ($entity instanceof DisconnectedMob)
                $entity->flagForDespawn();
        }
    }
    
    /**
     * @return Disconnected[]
     */
    public function getAllDisconnected(): array
    {
        return $this->disconnected;
    }
    
    /**
     * @param string $xuid
     * @return Disconnected|null
     */
    public function getDisconnected(string $xuid): ?Disconnected
    {
        return $this->disconnected[$xuid] ?? null;
    }
    
    /**
     * @param Player $player
     */
    public function addDisconnected(Player $player): void
    {
        $this->disconnected[$player->getXuid()] = new Disconnected($player->getXuid(), $player->getName(), $player->getHealth(), $player->getLocation(), $player->getInventory()->getContents(), $player->getArmorInventory()->getContents());
    }
    
    /**
     * @param string $xuid
     */
    public function removeDisconnected(string $xuid): void
    {
        unset($this->disconnected[$xuid]);
    }
}