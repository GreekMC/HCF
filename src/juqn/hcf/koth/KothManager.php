<?php

declare(strict_types=1);

namespace juqn\hcf\koth;

use juqn\hcf\HCFLoader;
use juqn\hcf\koth\command\KothCommand;

/**
 * Class KothManager
 * @package juqn\hcf\koth
 */
class KothManager
{
    
    /** @var Koth[] */
    private array $koths = [];
    /** @var string|null */
    private ?string $kothActive = null;
    
    /**
     * KothManager construct.
     */
    public function __construct()
    {
        # Register command
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new KothCommand());
        # Register koths
        foreach (HCFLoader::getInstance()->getProvider()->getKoths() as $name => $data)
            $this->createKoth($name, (int) $data['time'], (int) $data['points'], $data['key'], $data['keyCount'], $data['coords'], $data['claim'], $data['capzone']);
    }
    
    /**
     * @return Koth[]
     */
    public function getKoths(): array
    {
        return $this->koths;
    }
    
    /**
     * @param string $name
     * @return Koth|null
     */
    public function getKoth(string $name): ?Koth
    {
        return $this->koths[$name] ?? null;
    }
    
    /**
     * @return string|null
     */
    public function getKothActive(): ?string
    {
        return $this->kothActive;
    }
    
    /**
     * @param string $name
     * @param int $time
     * @param int $points
     * @param string $key
     * @param int $keyCount
     * @paran string|null $coords
     * @param array|null $claim
     * @param array|null $capzone
     */
    public function createKoth(string $name, int $time, int $points = 2, string $key = 'koth', int $keyCount = 1, ?string $coords = null, ?array $claim = null, ?array $capzone = null): void
    {
        $this->koths[$name] = new Koth($name, $time, $points, $key, $keyCount, $coords, $claim, $capzone);
    }
    
    /**
     * @param string $name
     */
    public function removeKoth(string $name): void
    {
        unset($this->koths[$name]);
    }
    
    /**
     * @param string|null $name
     */
    public function setKothActive(?string $name): void
    {
        $this->kothActive = $name;
    }
}
