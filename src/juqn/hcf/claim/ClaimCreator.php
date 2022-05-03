<?php

declare(strict_types=1);

namespace juqn\hcf\claim;

use juqn\hcf\HCFLoader;
use pocketmine\world\Position;

/**
 * Class ClaimCreator
 * @package juqn\hcf\claim
 */
class ClaimCreator extends Claim
{
    
    /** @var int */
    private int $minY, $maxY;
    
    /** @var Position|null */
    private ?Position $first = null, $second = null;
    
    /**
     * ClaimCreator construct.
     * @param string $name
     * @param string $type
     */
    public function __construct(string $name, string $type)
    {
        parent::__construct($name, $type, -1, -1, -1, -1, '-1');
        $this->minY = -1;
        $this->maxY = -1;
    }
    
    /**
     * @return int
     */
    public function getMinY(): int
    {
        return $this->minY;
    }
    
    /**
     * @return int
     */
    public function getMaxY(): int
    {
        return $this->maxY;
    }
    
    /**
     * @return Position|null
     */
    public function getFirst(): ?Position
    {
        return $this->first;
    }
    
    /**
     * @return Position|null
     */
    public function getSecond(): ?Position
    {
        return $this->second;
    }
    
    /**
     * @param Position $position
     * @param bool $first
     * @return bool
     */
    public function calculate(Position $position, bool $first = true): bool
    {
        if ($first)
            $this->first = $position;
        else $this->second = $position;
        
        if ($this->first !== null && $this->second !== null) {
            $this->minX = min($this->first->getFloorX(), $this->second->getFloorX());
            $this->maxX = max($this->first->getFloorX(), $this->second->getFloorX());
            
            $this->minY = min($this->first->getFloorY(), $this->second->getFloorY());
            $this->maxY = max($this->first->getFloorY(), $this->second->getFloorY());
            
            $this->minZ = min($this->first->getFloorZ(), $this->second->getFloorZ());
            $this->maxZ = max($this->first->getFloorZ(), $this->second->getFloorZ());
            
            if ($this->first->getWorld()->getFolderName() !== $this->second->getWorld()->getFolderName())
                return false;
            $this->world = $this->first->getWorld()->getFolderName();
            return true;
        }
        return false;
    }
    
    /**
     * @return int
     */
    public function calculateValue(): int
    {
        if ($this->first !== null && $this->second !== null) {
            [$minX, $maxX, $minZ, $maxZ] = [$this->getMinX(), $this->getMaxX(), $this->getMinZ(), $this->getMaxZ()];
            $minValue = (($maxX - $minX) + ($maxZ - $minZ)) / 4;
            $minValue *= 90;
            
            return (int) round(abs($minValue));
        }
        return 0;
    }

    /**
     * @param Position $first
     * @param Position $second
     * @return bool
     */
    public function calculateClaim(Position $first, Position $second): bool
    {
        $minX = min($first->getX(), $second->getX());
        $maxX = max($first->getX(), $second->getX());

        $minZ = min($first->getZ(), $second->getZ());
        $maxZ = max($first->getZ(), $second->getZ());

        for ($x = $minX; $x <= $maxX; $x++) {
            for ($z = $minZ; $z <= $maxZ; $z++) {
                $position = new Position($x, 0, $z, $first->getWorld());

                if (HCFLoader::getInstance()->getClaimManager()->insideClaim($position) !== null)
                    return true;
            }
        }
        return false;
    }
    
    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->getWorld() !== '-1' && $this->getMinX() !== -1 && $this->getMaxX() !== -1 && $this->getMinY() !== -1 && $this->getMaxY() !== -1 && $this->getMinZ() !== -1 && $this->getMaxZ() !== -1;
    }
}