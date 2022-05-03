<?php

declare(strict_types=1);

namespace juqn\hcf\faction;

use juqn\hcf\player\Player;

/**
 * Class FactionInvite
 * @package juqn\hcf\faction
 */
class FactionInvite
{
    
    /**
     * FactionInvite construct.
     * @param Player $player
     * @param string $faction
     * @param int $time
     */
    public function __construct(
        private Player $player,
        private string $faction,
        private int $time
    ) {
    }
    
    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }
    
    /**
     * @return string
     */
    public function getFaction(): string
    {
        return $this->faction;
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
    public function isExpired(): bool
    {
        return time() > $this->time;
    }
}