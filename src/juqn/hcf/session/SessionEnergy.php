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
    /** @var bool */
    private bool $paused;

    /**
     * Energy construct
     * @param string $format
     * @param int $energy
     * @param bool $paused
     */
    public function __construct(string $format, int $energy, bool $paused)
    {
        $this->format = $format;
        $this->energy = $energy;
        $this->paused = $paused;
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
     * @return bool
     */
    public function isPaused(): bool
    {
        return $this->paused;
    }

    /**
     * @param bool $value
     */
    public function setPaused(bool $value): void
    {
        $this->paused = $value;
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

    public function update(): void
    {
            $this->energy++;
    }
}