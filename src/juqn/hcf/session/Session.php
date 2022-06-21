<?php

declare(strict_types=1);

namespace juqn\hcf\session;

use juqn\hcf\HCFLoader;
use juqn\hcf\kit\classes\HCFClass;

/**
 * Class Session
 * @package juqn\hcf\session
 */
class Session
{
    
    /** @var string */
    private string $xuid;
    /** @var string */
    private string $name;
    
    /** @var string|null */
    private ?string $faction = null;
    
    /** @var int */
    private int $balance;
    /** @var int */
    private int $crystals;
    
    /** @var int */
    private int $kills;
    /** @var int */
    private int $deaths;
    /** @var int */
    private int $killStreak;
    /** @var int */
    private int $highestKillStreak;
    
    /** @var SessionCooldown[] */
    private array $cooldowns = [];
    /** @var SessionEnergy[] */
    private array $energies = [];

    /** @var bool */
    private bool $autoFeed = false;
    
    /**
     * Session construct.
     * @param string $xuid
     * @param array $data
     * @param bool $firstTime
     */
    public function __construct(string $xuid, array $data, bool $firstTime)
    {
        $this->xuid = $xuid;
        $this->name = $data['name'];
        if ($data['faction'] !== null && HCFLoader::getInstance()->getFactionManager()->getFaction($data['faction']) !== null)
            $this->faction = $data['faction'];
        
        $this->balance = (int) $data['balance'];
        $this->crystals = (int) $data['crystals'];
        
        $this->kills = (int) $data['stats']['kills'];
        $this->deaths = (int) $data['stats']['deaths'];
        $this->killStreak = (int) $data['stats']['killStreak'];
        $this->highestKillStreak = (int) $data['stats']['highestKillStreak'];
        
        foreach ($data['cooldowns'] as $key => $d)
            $this->addCooldown($key, $d['format'], (int) $d['time'], $d['paused'], $d['visible']);
            
        foreach ($data['energies'] as $key => $d)
            $this->addEnergy($key, $d['format'], (int) $d['energy']);
        
        if ($firstTime)
            $this->addCooldown('starting.timer', '&l&aStarting Timer&r&7: &r&c', 60 * 60);
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
     * @return string|null
     */
    public function getFaction(): ?string
    {
        return $this->faction;
    }
    
    /**
     * @return int
     */
    public function getBalance(): int
    {
        return $this->balance;
    }
    
    /**
     * @return int
     */
    public function getCrystals(): int
    {
        return $this->crystals;
    }
    
    /**
     * @return int
     */
    public function getKills(): int
    {
        return $this->kills;
    }
    
    /**
     * @return int
     */
    public function getDeaths(): int
    {
        return $this->deaths;
    }
    
    /**
     * @return int
     */
    public function getKillStreak(): int
    {
        return $this->killStreak;
    }
    
    /**
     * @return int
     */
    public function getHighestKillStreak(): int
    {
        return $this->highestKillStreak;
    }
    
    /**
     * @return SessionCooldown[]
     */
    public function getCooldowns(): array
    {
        return $this->cooldowns;
    }
    
    /**
     * @return SessionEnergy[]
     */
    public function getEnergies(): array
    {
        return $this->energies;
    }
    
    /**
     * @param string $key
     * @return SessionCooldown|null
     */
    public function getCooldown(string $key): ?SessionCooldown
    {
        return $this->cooldowns[$key] ?? null;
    }
    
    /**
     * @param string $key
     * @return SessionEnergy|null
     */
    public function getEnergy(string $key): ?SessionEnergy
    {
        return $this->energies[$key] ?? null;
    }

    /**
     * @return bool
     */
    public function hasAutoFeed(): bool
    {
        return $this->autoFeed;
    }
    
    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    
    /**
     * @param string|null $factionName
     */
    public function setFaction(?string $factionName): void
    {
        $this->faction = $factionName;
    }
    
    /**
     * @param int $balance
     */
    public function setBalance(int $balance): void
    {
        $this->balance = $balance;
    }
    
    /**
     * @param int $crystals
     */
    public function setCrystals(int $crystals): void
    {
        $this->crystals = $crystals;
    }
    
    public function addKill(): void
    {
        $this->kills++;
    }
    
    public function removeKill(): void
    {
        $this->kills--;
    }
    
    public function addDeath(): void
    {
        $this->deaths++;
    }
    
    public function removeDeath(): void
    {
        $this->deaths--;
    }
    
    /**
     * @param int $amount
     */
    public function setKillStreak(int $amount): void
    {
        $this->killStreak = $amount;
    }
    
    public function addKillStreak(): void
    {
        $this->killStreak++;
    }
    
    public function removeKillStreak(): void
    {
        $this->killStreak--;
    }
    
    public function addHighestKillStreak(): void
    {
        $this->highestKillStreak++;
    }
    
    public function removeHighestKillStreak(): void
    {
        $this->highestKillStreak--;
    }
    
    /**
     * @param string $key
     * @param string $format
     * @param int $time
     * @param bool $paused
     * @param bool $visible
     */
    public function addCooldown(string $key, string $format, int $time, bool $paused = false, bool $visible = true): void
    {
        $this->cooldowns[$key] = new SessionCooldown($format, $time, $paused, $visible);
    }
    
    /**
     * @param string $key
     * @param string $format
     * @param int $energy
     */
    public function addEnergy(string $key, string $format, int $energy = 0): void
    {
        $this->energies[$key] = new SessionEnergy($format, $energy);
    }
    
    /**
     * @param string $key
     */
    public function removeCooldown(string $key): void
    {
        unset($this->cooldowns[$key]);
    }
    
    /**
     * @param string $key
     */
    public function removeEnergy(string $key): void
    {
        unset($this->energies[$key]);
    }

    /**
     * @param bool $value
     */
    public function setAutoFeed(bool $value): void
    {
        $this->autoFeed = $value;
    }
    
    public function onUpdate(): void
    {
         foreach ($this->getCooldowns() as $key => $cooldown) {
            $cooldown->update();
            
            if ($cooldown->getTime() <= 0)
                $this->removeCooldown($key);
        }
    }
    
    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [
            'name' => $this->getName(),
            'faction' => $this->getFaction(),
            'balance' => $this->getBalance(),
            'crystals' => $this->getCrystals(),
            'cooldowns' => [],
            'energies' => [],
            'stats' => [
                'kills' => $this->getKills(),
                'deaths' => $this->getDeaths(),
                'killStreak' => $this->getKillStreak(),
                'highestKillStreak' => $this->getHighestKillStreak()
            ]
        ];
        
        foreach ($this->getCooldowns() as $key => $cooldown)
            $data['cooldowns'][$key] = [
                'format' => $cooldown->getFormat(),
                'time' => $cooldown->getTime(),
                'paused' => $cooldown->isPaused(),
                'visible' => $cooldown->isVisible()
            ];
        
        foreach ($this->getEnergies() as $key => $energy)
            $data['energies'][$key] = [
                'format' => $energy->getFormat(),
                'energy' => $energy->getEnergy()
            ];
        return $data;
    }
}