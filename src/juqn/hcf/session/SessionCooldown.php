<?php

declare(strict_types=1);

namespace juqn\hcf\session;

/**
 * Class SessionCooldown
 * @package juqn\hcf\session
 */
class SessionCooldown
{
    
    /** @var string */
    private string $format;
    /** @var int */
    private int $time;
    /** @var bool */
    private bool $paused;
    /** @var bool */
    private bool $visible;
    
    /**
     * Cooldown construct.
     * @param string $format
     * @param int $time
     * @param bool $paused
     * @param bool $visible
     */
    public function __construct(string $format, int $time, bool $paused, bool $visible)
    {
        $this->format = $format;
        $this->time = $time;
        $this->paused = $paused;
        $this->visible = $visible;
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
    public function getTime(): int
    {
        return $this->time;
    }
    
    /**
     * @return bool
     */
    public function isPaused(): bool
    {
        return $this->paused;
    }
    
    /**
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }
    
    /**
     * @param int $time
     */
    public function setTime(int $time): void
    {
        $this->time = $time;
    }
    
    /**
     * @param bool $value
     */
    public function setPaused(bool $value): void
    {
        $this->paused = $value;
    }
    
    /**
     * @param bool $value
     */
    public function setVisible(bool $value): void
    {
        $this->visible = $value;
    }
    
    public function update(): void
    {
        if (!$this->isPaused())
            $this->time--;
    }
}