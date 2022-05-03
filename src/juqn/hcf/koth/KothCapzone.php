<?php

declare(strict_types=1);

namespace juqn\hcf\koth;

use pocketmine\world\Position;

/**
 * Class KothCapzone
 * @package juqn\hcf\koth
 */
class KothCapzone
{
    
    /** @var int */
    private int $minX, $minY, $minZ;
    /** @var int */
    private int $maxX, $maxY, $maxZ;
    /** @var string */
    private string $world;
    
    /**
     * KothCapzone construct.
     * @param int $minX
     * @param int $maxX
     * @param int $minY
     * @param int $maxY
     * @param int $minZ
     * @param int $maxZ
     * @param string $world
     */
    public function __construct(int $minX, int $maxX, int $minY, int $maxY, int $minZ, int $maxZ, string $world)
    {
        $this->minX = $minX;
        $this->maxX = $maxX;
        $this->minY = $minY;
        $this->maxY = $maxY;
        $this->minZ = $minZ;
        $this->maxZ = $maxZ;
        $this->world = $world;
    }
    
    /**
     * @return int
     */
    public function getMinX(): int
    {
        return $this->minX;
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
    public function getMinZ(): int
    {
        return $this->minZ;
    }
    
    /**
     * @return int
     */
    public function getMaxX(): int
    {
        return $this->maxX;
    }
    
    /**
     * @return int
     */
    public function getMaxY(): int
    {
        return $this->maxY;
    }
    
    /**
     * @return int
     */
    public function getMaxZ(): int
    {
        return $this->maxZ;
    }
    
    /**
     * @return string
     */
    public function getWorld(): string
    {
        return $this->world;
    }
    
    /**
     * @param Position $position
     * @return bool
     */
    public function inside(Position $position): bool
    {
        return $this->getWorld() === $position->getWorld()->getFolderName() && $this->getMinX() <= $position->getX() && $this->getMaxX() >= $position->getFloorX() && $this->getMinY() <= $position->getY() && $this->getMaxY() >= $position->getFloorY() &&  $this->getMinZ() <= $position->getZ() && $this->getMaxZ() >= $position->getFloorZ();
    }
}