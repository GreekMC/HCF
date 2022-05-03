<?php

declare(strict_types=1);

namespace juqn\hcf\claim;

use juqn\hcf\HCFLoader;

use pocketmine\world\Position;

/**
 * Class ClaimManager
 * @package juqn\hcf\claim
 */
class ClaimManager
{
    
    /** @var Claim[] */
    private array $claims = [];
    /** @var ClaimCreator[] */
    private array $creators = [];
    
    /**
     * ClaimManager construct.
     */
    public function __construct()
    {
        # Register handler
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new ClaimListener(), HCFLoader::getInstance());
    }
    
    /**
     * @return Claim[]
     */
    public function getClaims(): array
    {
        return $this->claims;
    }
    
    /**
     * @return ClaimCreator[]
     */
    public function getCreators(): array
    {
        return $this->creators;
    }
    
    /**
     * @param string $claimName
     * @return Claim|null
     */
    public function getClaim(string $claimName): ?Claim
    {
        return $this->claims[$claimName] ?? null;
    }
    
    /**
     * @param Position $position
     * @return Claim|null
     */
    public function insideClaim(Position $position): ?Claim
    {
        $claim = array_values(
            array_filter(
                $this->claims,
                function (Claim $claim) use ($position): bool {
                    return $claim->inside($position);
                }
            )
        );
        
        if (isset($claim[0]))
            return $claim[0];
        return null;
    }

    /**
     * @param string $player
     * @return ClaimCreator|null
     */
    public function getCreator(string $player): ?ClaimCreator
    {
        return $this->creators[$player] ?? null;
    }
    
    /**
     * @param string $claimName
     * @return bool
     */
    public function getCreateByClaimName(string $claimName): bool
    {
        return count(
            array_filter(
                $this->creators,
                function (ClaimCreator $creator) use ($claimName): bool {
                    return $claimName === $creator->getName();
                 }
            )
        ) !== 0;
    }
    
    /**
     * @param string $name
     * @param string $type
     * @param int $minX
     * @param int $maxX
     * @param int $minZ
     * @param int $maxZ
     * @param string $world
     */
    public function createClaim(string $name, string $type, int $minX, int $maxX, int $minZ, int $maxZ, string $world): void
    {
        $this->claims[$name] = new Claim($name, $type, $minX, $maxX, $minZ, $maxZ, $world);
    }
    
    /**
     * @param string $player
     * @param string $name
     * @param string $type
     */
    public function createCreator(string $player, string $name, string $type): void
    {
        $this->creators[$player] = new ClaimCreator($name, $type);
    }
    
    /**
     * @param string $name
     */
    public function removeClaim(string $name): void
    {
        unset($this->claims[$name]);
    }
    
    /**
     * @param string $player
     */
    public function removeCreator(string $player): void
    {
        unset($this->creators[$player]);
    }
}