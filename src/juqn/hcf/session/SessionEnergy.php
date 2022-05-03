<?php

declare(strict_types=1);

namespace juqn\hcf\session;

/**
 * Class SessionEnergy
 * @package juqn\hcf\session
 */
class SessionEnergy
{
    
    /** @var string */
    private string $format;
    /** @var int */
    private int $energy;
    
    /**
     * Energy construct
     * @param string $format
     * @param int $energy
     */
    public function __construct(string $format, int $energy)
    {
        $this->format = $format;
        $this->energy = $energy;
    }
    
    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }
    
    /**
     * @return int
     */
    public function getEnergy(): int
    {
        return $this->energy;
    }
    
    /**
     * @param int $amount
     */
    public function addEnergy(int $amount): void
    {
        $this->energy += $amount;
    }
    
    /**
     * @param int $amount
     */
    public function reduceEnergy(int $amount): void
    {
        $this->energy -= $amount;
    }
}