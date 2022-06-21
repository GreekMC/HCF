<?php

declare(strict_types=1);

namespace juqn\hcf\player;

use juqn\hcf\enchantment\Enchantment;
use juqn\hcf\HCFLoader;
use juqn\hcf\kit\classes\ClassFactory;
use juqn\hcf\kit\classes\HCFClass;
use juqn\hcf\session\Session;
use juqn\hcf\utils\Timer;

use pocketmine\entity\Location;
use pocketmine\player\Player as BasePlayer;
use pocketmine\player\PlayerInfo;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

/**
 * Class Player
 * @package juqn\hcf\player
 */
class Player extends BasePlayer
{
    
    /** @var int|float  */
    private int|float $lastCheck = -1;
    /** @var PlayerScoreboard */
    private PlayerScoreboard $scoreboard;
    /** @var HCFClass|null */
    private ?HCFClass $class = null;
    /** @var string|null */
    private ?string $currentClaim = null;
    
    /** @var bool */
    private bool $god = false;

    /**
     * Player construct.
     * @param Server $server
     * @param NetworkSession $session
     * @param PlayerInfo $playerInfo
     * @param bool $authenticated
     * @param Location $spawnLocation
     * @param CompoundTag|null $namedtag
     */
    public function __construct(Server $server, NetworkSession $session, PlayerInfo $playerInfo, bool $authenticated, Location $spawnLocation, ?CompoundTag $namedtag)
    {
        parent::__construct($server, $session, $playerInfo, $authenticated, $spawnLocation, $namedtag);
        $this->scoreboard = new PlayerScoreboard($this);
    }
    
    /**
     * @return PlayerScoreboard
     */
    public function getScoreboard(): PlayerScoreboard
    {
        return $this->scoreboard;
    }
   
    /**
     * @return HCFClass|null
     */ 
    public function getClass(): ?HCFClass
    {
        return $this->class;
    }
    
    /**
     * @return string|null
     */
    public function getCurrentClaim(): ?string
    {
        return $this->currentClaim;
    }
    
    /**
     * @return bool
     */
    public function isGod(): bool
    {
        return $this->god;
    }
    
    /**
     * @return Session|null
     */
    public function getSession(): ?Session
    {
        return HCFLoader::getInstance()->getSessionManager()->getSession($this->getXuid());
    }
    
    /**
     * @param HCFClass|null $class
     */
    public function setClass(?HCFClass $class): void
    {
        $this->class = $class;
    }
    
    /**
     * @param string|null $claimName
     */
    public function setCurrentClaim(?string $claimName = null): void
    {
        $this->currentClaim = $claimName;
    }
    
    /**
     * @param bool $value
     */
    public function setGod(bool $value): void
    {
        $this->god = $value;
    }
    
    public function join(): void
    {
        # Scoreboard setup
        $this->scoreboard->init();
        
        # Scoretag & Nametag setup
        $this->setNameTag(TextFormat::colorize('&c' . $this->getName()));
        
        if ($this->getSession()->getFaction() !== null) {
            $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($this->getSession()->getFaction());
            $this->setScoreTag(TextFormat::colorize('&6[&c' . $faction->getName() . ' ' . ($faction->getDtr() === (count($faction->getMembers()) + 0.1) ? '&a' : '&c') . $faction->getDtr() . '■&6]'));
        }
        
        # Add coordinates
        $pk = GameRulesChangedPacket::create([
            'showCoordinates' => new BoolGameRule(true, false)
        ]);
        $this->getNetworkSession()->sendDataPacket($pk);
    }
    
    private function updateScoreboard(): void
    {
        $lines = [
            TextFormat::colorize('&7')
        ];
        
        # Events
        if (($sotw = HCFLoader::getInstance()->getEventManager()->getSotw())->isActive())
            $lines[] = TextFormat::colorize(' ' . $sotw->getFormat() . Timer::format($sotw->getTime()));
       if (($eotw = HCFLoader::getInstance()->getEventManager()->getEotw())->isActive())
           $lines[] = TextFormat::colorize(' ' . $eotw->getFormat() . Timer::format($eotw->getTime()));
        
        # Koth
        if (($kothName = HCFLoader::getInstance()->getKothManager()->getKothActive()) !== null) {
            $koth = HCFLoader::getInstance()->getKothManager()->getKoth($kothName);
            
            if ($koth !== null) $lines[] = TextFormat::colorize('&9&l ' . $koth->getName() . '&r&7: &r&c' . Timer::format($koth->getProgress()));
        }
        
        # Cooldowns
        foreach ($this->getSession()->getCooldowns() as $cooldown) {
            if ($cooldown->isVisible())
                $lines[] = TextFormat::colorize(' ' . $cooldown->getFormat() . Timer::format($cooldown->getTime()));
        }

        # Energy
        foreach ($this->getSession()->getEnergies() as $energy) {
                $lines[] = TextFormat::colorize(' ' . $energy->getFormat() . ($energy->getEnergy().'.0'));
        }
        
        # Faction
        if ($this->getSession()->getFaction() !== null) {
            $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($this->getSession()->getFaction());
            
            # Focus
            if ($faction->getFocus() !== null) {
                if (($targetFaction = HCFLoader::getInstance()->getFactionManager()->getFaction($faction->getFocus())) !== null) {
                    if (count($lines) > 1) $lines[] = TextFormat::colorize('&r ');
                    $lines[] = TextFormat::colorize(' &l&6Team&r&7: &e' . $targetFaction->getName());
                    $lines[] = TextFormat::colorize(' &l&6HQ&r&7: &e' . ($targetFaction->getHome() !== null ? $targetFaction->getHome()->getFloorX() . ', ' . $targetFaction->getHome()->getFloorZ() : 'Has no home'));
                    $lines[] = TextFormat::colorize(' &l&6DTR&r&7: &e' . $targetFaction->getDtr() . ' &c■');
                    $lines[] = TextFormat::colorize(' &l&6Online&r&7: &e' . count($targetFaction->getOnlineMembers()));
                }
            }
            
            # Rally
            if (($rally = $faction->getRally()) !== null) {
                if (count($lines) > 1) $lines[] = TextFormat::colorize('&r&r ');
                $lines[] = TextFormat::colorize(' &l&6Rally&r&7: &e' . $rally[0]);
                $lines[] = TextFormat::colorize(' &l&6XYZ&r&7: &e' . $rally[1]->getFloorX() . ', ' . $rally[1]->getFloorY() . ', ' . $rally[1]->getFloorZ());
            }
        }
        
        $lines[] = TextFormat::colorize('&7&r');
        
        if (count($lines) === 2) {
            if ($this->scoreboard->isSpawned()) $this->scoreboard->remove();
            return;
        }
        
        if (!$this->scoreboard->isSpawned())
            $this->scoreboard->init();
        else $this->scoreboard->clear();
        
        foreach ($lines as $line => $content)
            $this->scoreboard->addLine($content);
    }

    /**
     * @param int $currentTick
     */
    public function onUpdate(int $currentTick): bool
    {
        $update = parent::onUpdate($currentTick);
        
        if ($update) {
            if ($currentTick % 20 === 0) {
                # Update custom enchants
                foreach ($this->getArmorInventory()->getContents() as $armor) {
                    foreach ($armor->getEnchantments() as $enchantment) {
                        $type = $enchantment->getType();

                        if ($type instanceof Enchantment)
                            $type->giveEffect($this);
                    }
                }
                
                # Update scoreboard
                $this->updateScoreboard();

                #update energys
                if($this->getClass() === null){
                    $this->getSession()->removeEnergy('bard.energy');
                }

                # Class event
                if ($this->getClass() !== null)
                    $this->getClass()->onRun($this);
                else {
                    foreach(ClassFactory::getClasses() as $class) {
                        if ($class->isActive($this)) {
                            $this->class = $class;
                            break;
                        }
                    }
                }
            }
        }
        return $update;
    }
    
    protected function processMostRecentMovements() : void
    {
        $micro = microtime(true);
        
        if ($micro - $this->lastCheck > 1) {
            $this->lastCheck = $micro;
            
            foreach ($this->getArmorInventory()->getContents() as $armor) {
                foreach ($armor->getEnchantments() as $enchantment) {
                    $type = $enchantment->getType();

                    if ($type instanceof Enchantment)
                        $type->handleMove($this);
                }
            }
        }
        parent::processMostRecentMovements();
    }
}
