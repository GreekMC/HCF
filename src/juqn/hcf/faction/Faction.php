<?php

declare(strict_types=1);

namespace juqn\hcf\faction;

use juqn\hcf\HCFLoader;
use juqn\hcf\session\Session;
use juqn\hcf\player\Player;

use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;

/**
 * Class Faction
 * @package juqn\hcf\faction
 */
class Faction
{
    
    /** @var string[] */
    private static array $types = [
        'Spawn' => 'spawn',
        'North Road' => 'road',
        'South Road' => 'road',
        'East Road' => 'road',
        'West Road' => 'road'
    ];
    
    /** @var string */
    private string $name;
    
    /** @var string[] */
    private array $roles;
    
    /** @var float */
    private float $dtr;
    /** @var int */
    private int $balance;
    /** @var int */
    private int $points;
    /** @var int */
    private int $kothCaptures;
    
    /** @var string|null */
    private ?string $focus = null;
    /** @var array|null */
    private ?array $rally = null;
    
    /** @var int|null */
    private ?int $timeRegeneration;
    
    /** @var Position|null */
    private ?Position $home = null;
    
    /**
     * Faction construct.
     * @param string $name
     * @param array $data
     */
    public function __construct(string $name, array $data)
    {
        $this->name = $name;
        $this->roles = $data['roles'];
        $this->dtr = (float) $data['dtr'];
        $this->balance = (int) $data['balance'];
        $this->points = (int) $data['points'];
        $this->kothCaptures = (int) $data['kothCaptures'];
        $this->timeRegeneration = (int) $data['timeRegeneration'];
        
        if ($data['home'] !== null)
            $this->home = new Position((int) $data['home']['x'], (int) $data['home']['y'], (int) $data['home']['z'], HCFLoader::getInstance()->getServer()->getWorldManager()->getWorldByName($data['home']['world']));
            
        if ($data['claim'] !== null) {
            $type = self::$types[$name] ?? 'faction';
            HCFLoader::getInstance()->getClaimManager()->createClaim($name, $type, $data['claim']['minX'], $data['claim']['maxX'], $data['claim']['minZ'], $data['claim']['maxZ'], $data['claim']['world']);
        }
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
    
    /**
     * @param string $name
     * @return string|null
     */
    public function getRole(string $name): ?string
    {
        return $this->roles[$name] ?? null;
    }
    
    /**
     * @return float
     */
    public function getDtr(): float
    {
        return $this->dtr;
    }
    
    /**
     * @return int
     */
    public function getBalance(): int
    {
        return $this->balance;
    }
    
    /**
     * @return int
     */
    public function getPoints(): int
    {
        return $this->points;
    }
    
    /**
     * @return int
     */
    public function getKothCaptures(): int
    {
        return $this->kothCaptures;
    }
    
    /**
     * @return string|null
     */
    public function getFocus(): ?string
    {
        return $this->focus;
    }
    
    /**
     * @return array|null
     */
    public function getRally(): ?array
    {
        return $this->rally;
    }
    
    /**
     * @return Position|null
     */
    public function getHome(): ?Position
    {
        return $this->home;
    }
    
    /**
     * @return int|null
     */
    public function getTimeRegeneration(): ?int
    {
        return $this->timeRegeneration;
    }
    
    /**
     * @param string $member
     * @param string $role
     */
    public function addRole(string $member, string $role): void
    {
        $this->roles[$member] = $role;
    }
    
    /**
     * @param string $member
     */
    public function removeRole(string $member): void
    {
        unset($this->roles[$member]);
    }
    
    /**
     * @param float $value
     */
    public function setDtr(float $value): void
    {
        $this->dtr = $value;
    }
    
    /**
     * @param int $value
     */
    public function setBalance(int $value): void
    {
        $this->balance = $value;
    }
    
    /**
     * @param int $value
     */
    public function setPoints(int $value): void
    {
        $this->points = $value;
    }
    
    /**
     * @param int $value
     */
    public function setKothCaptures(int $value): void
    {
        $this->kothCaptures = $value;
    }
    
    /**
     * @param string|null $value
     */
    public function setFocus(?string $value): void
    {
        $this->focus = $value;
    }
    
    /**
     * @param array|null $value
     */
    public function setRally(?array $value): void
    {
        $this->rally = $value;
    }
    
    /**
     * @param Position|null $value
     */
    public function setHome(?Position $value): void
    {
        $this->home = $value;
    }
    
    /**
     * @param int|null $value
     */
    public function setTimeRegeneration(?int $value): void
    {
        $this->timeRegeneration = $value;
    }
    
    /**
     * @return Session[]
     */
    public function getMembers(): array
    {
        return array_filter(HCFLoader::getInstance()->getSessionManager()->getSessions(), function (Session $session): bool {
            return $session->getFaction() !== null && $session->getFaction() === $this->getName();
        });
    }
    
    /**
     * @return Player[]
     */
    public function getOnlineMembers(): array
    {
        return array_filter(Server::getInstance()->getOnlinePlayers(), function (\pocketmine\player\Player $player): bool {
            return $player instanceof Player && $player->getSession()->getFaction() === $this->getName();
        });
    }
    
    public function onUpdate(): void
    {
        if ($this->getTimeRegeneration() !== null) {
            if (HCFLoader::getInstance()->getConfig()->get('facion.regeneration.offline', true) === false && count($this->getOnlineMembers()) === 0)
                return;
            $this->timeRegeneration--;
            
            if ($this->timeRegeneration === 0) {
                $this->timeRegeneration = null;
                $this->setDtr(0.01 + (count($this->getMembers()) * 1.00));
                
                # Setup scoretag for team members
                foreach ($this->getOnlineMembers() as $member)
                    $member->setScoreTag(TextFormat::colorize('&6[&c' . $this->getName() . ' &a' . $this->getDtr() . '■&6]'));
            }
        }
    }
    
    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [
            'roles' => $this->getRoles(),
            'dtr' => $this->getDtr(),
            'balance' => $this->getBalance(),
            'points' => $this->getPoints(),
            'kothCaptures' => $this->getKothCaptures(),
            'timeRegeneration' => $this->getTimeRegeneration(),
            'home' => null,
            'claim' => null
        ];
        
        if ($this->getHome() !== null)
            $data['home'] = [
                'x' => $this->getHome()->getFloorX(),
                'y' => $this->getHome()->getFloorY(),
                'z' => $this->getHome()->getFloorZ(),
                'world' => $this->getHome()->getWorld()->getFolderName()
            ];
        
        if (($claim = HCFLoader::getInstance()->getClaimManager()->getClaim($this->getName())) !== null)
            $data['claim'] = [
                'minX' => $claim->getMinX(),
                'maxX' => $claim->getMaxX(),
                'minZ' => $claim->getMinZ(),
                'maxZ' => $claim->getMaxZ(),
                'world' => $claim->getWorld()
            ];
        return $data;
    }
}