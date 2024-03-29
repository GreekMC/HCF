<?php

declare(strict_types=1);

namespace juqn\hcf\faction;

use juqn\hcf\faction\command\FactionCommand;
use juqn\hcf\HCFLoader;
use juqn\hcf\player\Player;

class FactionManager
{
    
    /** @var Faction[] */
    private array $factions = [];
    /** @var FactionInvite[] */
    private array $invites = [];
    
    /**
     * FactionManager construct.
     */
    public function __construct()
    {
        # Register command
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new FactionCommand());
        # Register event handler
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new FactionListener(), HCFLoader::getInstance());
        # Register factions
        foreach (HCFLoader::getInstance()->getProvider()->getFactions() as $name => $data)
            $this->createFaction((string) $name, $data);
    }
    
    /**
     * @return Faction[]
     */
    public function getFactions(): array
    {
        return $this->factions;
    }
    
    /**
     * @param string $xuid
     * @return FactionInvite[]|null
     */
    public function getInvites(string $xuid): ?array
    {
        return $this->invites[$xuid] ?? null;
    }
    
    /**
     * @param string $name
     * @return Faction|null
     */
    public function getFaction(string $name): ?Faction
    {
        return $this->factions[$name] ?? null;
    }
    
    /**
     * @param string $name
     * @param array $data
     */
    public function createFaction(string $name, array $data): void
    {
        $this->factions[$name] = new Faction($name, $data);
    }
    
    /**
     * @param Player $player
     * @param Player $target
     */
    public function createInvite(Player $player, Player $target): void
    {
        $this->invites[$target->getXuid()][$player->getSession()->getFaction()] = new FactionInvite($player, $player->getSession()->getFaction(), time() + 60);
    }

    /**
     * @param Player $player
     * @param string $target
     */
    public function removeInvite(Player $player, string $target): void
    {
        unset($this->invites[$player->getXuid()][$target]);
    }
    
    /**
     * @param Player $player
     */
    public function removeInvites(Player $player): void
    {
        unset($this->invites[$player->getXuid()]);
    }
    
    /**
     * @param string $name
     */
    public function removeFaction(string $name): void
    {
        unset($this->factions[$name]);
        
        if (file_exists(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'factions' . DIRECTORY_SEPARATOR . $name . '.json')) {
            $result = unlink(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'factions' . DIRECTORY_SEPARATOR . $name . '.json');

            if ($result) {
                HCFLoader::getInstance()->getLogger()->debug('Faction ' . $name . ' file deleted successfully');
            } else {
                HCFLoader::getInstance()->getLogger()->debug('Error for deleted faction ' . $name . ' file');
            }
        }
    }
}