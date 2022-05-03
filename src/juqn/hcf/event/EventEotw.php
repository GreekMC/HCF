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
        private string $format = '&l&bSOTW end in: &r&7:',
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
}