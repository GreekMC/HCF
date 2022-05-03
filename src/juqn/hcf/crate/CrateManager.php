<?php

declare(strict_types=1);

namespace juqn\hcf\crate;

use juqn\hcf\crate\command\CrateCommand;
use juqn\hcf\crate\tile\CrateTile;
use juqn\hcf\entity\TextEntity;
use juqn\hcf\HCFLoader;

use pocketmine\block\tile\TileFactory;

/**
 * Class CrateManager
 * @package juqn\hcf\crate
 */
class CrateManager
{
    
    /** @var Crate[] */
    private array $crates = [];
    /** @var array */
    private array $creators = [];
    
    /**
     * CrateManager construct.
     */
    public function __construct()
    {
        # Register tile
        TileFactory::getInstance()->register(CrateTile::class);
        # Register handler
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new CrateListener(), HCFLoader::getInstance());
        # Register command
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new CrateCommand());
        # Register crates
        foreach (HCFLoader::getInstance()->getProvider()->getCrates() as $name => $data)
            $this->addCrate($name, $data['keyId'], $data['keyFormat'], $data['nameFormat'], $data['items']);
    }
    
    public function onDisable(): void
    {
        foreach (HCFLoader::getInstance()->getServer()->getWorldManager()->getWorlds() as $world) {
            foreach ($world->getEntities() as $entity) {
                if ($entity instanceof TextEntity) $entity->close();
            }
        }
    }
    
    /**
     * @return Crate[]
     */
    public function getCrates(): array
    {
        return $this->crates;
    }
    
    /**
     * @return array
     */
    public function getCreators(): array
    {
        return $this->creators;
    }
    
    /**
     * @param string $crateName
     * @return Crate|null
     */
    public function getCrate(string $crateName): ?Crate
    {
        return $this->crates[$crateName] ?? null;
    }
    
    /**
     * @param string $playerName
     * @return array|null
     */
    public function getCreator(string $playerName): ?array
    {
        return $this->creators[$playerName] ?? null;
    }
    
    /**
     * @param string $crateName
     * @param string $keyId
     * @param string $keyFormat
     * @param string $nameFormat
     * @param array $items
     */
    public function addCrate(string $crateName, string $keyId, string $keyFormat, string $nameFormat, array $items): void
    {
        $this->crates[$crateName] = new Crate($crateName, $keyId, $keyFormat, $nameFormat, $items);
    }

    /**
     * @param string $playerName
     * @param array $data
     */ 
    public function addCreator(string $playerName, array $data): void
    {
        $this->creators[$playerName] = $data;
    }
    
    /**
     * @param string $crateName
     */
    public function removeCrate(string $crateName): void
    {
        unset($this->crates[$crateName]);
    }
    
    /**
     * @param string $playerName
     */
    public function removeCreator(string $playerName): void
    {
        unset($this->creators[$playerName]);
    }
}