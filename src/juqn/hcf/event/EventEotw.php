<?php

declare(strict_types=1);

namespace juqn\hcf\event;

/**
 * Class EventEotw
 * @package juqn\hcf\event
 */
class EventEotw
{
    
    /**
     * EventEotw construct.
     * @param int $time
     * @param string $format
     * @param bool $active
     */
    public function __construct(
        private int $time = 60 * 60,
        private string $format = '&l&cEOTW Ends in&r&7: &r&7',
        private bool $active = false
    ) {}
    
    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }
    
    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }
    
    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }
    
    /**
     * @param int $value
     */
    public function setTime(int $value): void
    {
        $this->time = $value;
    }
    
    /**
     * @param bool $value
     */
    public function setActive(bool $value): void
    {
        $this->active = $value;
    }

    public function update(): void
    {
        if ($this->active) {
            $this->time--;

            if ($this->time <= 0) {
                $this->active = false;
                $this->time = 60 * 60;
            }
        }
    }
}