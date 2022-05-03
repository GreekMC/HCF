<?php

declare(strict_types=1);

namespace juqn\hcf\claim;

use pocketmine\world\Position;

/**
 * Class Claim
 * @package juqn\hcf\claim
 */
class Claim
{
    
    /** @var string */
    protected string $name, $type;
    
    /** @var int */
    protected int $minX, $minZ;
    /** @var int */
    protected int $maxX, $maxZ;
    /** @var string */
    protected string $world;
    
    /**
     * Claim construct.
     * @param string $name
     * @param string $type
     * @param int $minX
     * @param int $maxX
     * @param int $minZ
     * @param int $maxZ
     * @param string $world
     */
    public function __construct(string $name, string $type, int $minX, int $maxX, int $minZ, int $maxZ, string $world)
    {
        $this->name = $name;
        $this->type = $type;
        $this->minX = $minX;
        $this->maxX = $maxX;
        $this->minZ = $minZ;
        $this->maxZ = $maxZ;
        $this->world = $world;
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
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
        return $this->getWorld() === $position->getWorld()->getFolderName() && $this->getMinX() <= $position->getX() && $this->getMaxX() >= $position->getFloorX() && $this->getMinZ() <= $position->getZ() && $this->getMaxZ() >= $position->getFloorZ();
    }
}