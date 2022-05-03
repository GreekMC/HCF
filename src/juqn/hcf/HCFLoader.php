<?php

declare(strict_types=1);

namespace juqn\hcf;

use juqn\hcf\entity\EntityManager;
use juqn\hcf\claim\ClaimManager;
use juqn\hcf\command\CommandManager;
use juqn\hcf\crate\CrateManager;
use juqn\hcf\enchantment\EnchantmentManager;
use juqn\hcf\event\EventManager;
use juqn\hcf\faction\FactionManager;
use juqn\hcf\item\ItemManager;
use juqn\hcf\kit\KitManager;
use juqn\hcf\koth\KothManager;
use juqn\hcf\reclaim\ReclaimManager;
use juqn\hcf\session\SessionManager;
use juqn\hcf\shop\ShopManager;
use juqn\hcf\provider\Provider;
use juqn\hcf\task\TaskHandler;
use juqn\hcf\vkit\vKitManager;

use muqsit\invmenu\InvMenuHandler;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;

/**
 * Class HCFLoader
 * @package juqn\hcf
 */
class HCFLoader extends PluginBase
{

    /** @var HCFLoader */
    private static HCFLoader $instance;
    
    /** @var Provider */
    private Provider $provider;
    
    /** @var EntityManager */
    private EntityManager $entityManager;
    /** @var ClaimManager */
    private ClaimManager $claimManager;
    /** @var CommandManager */
    private CommandManager $commandManager;
    /** @var CrateManager */
    private CrateManager $crateManager;
    /** @var EnchantmentManager */
    private EnchantmentManager $enchantmentManager;
    /** @var EventManager */
    private EventManager $eventManager;
    /** @var FactionManager */
    private FactionManager $factionManager;
    /** @var ItemManager */
    private ItemManager $itemManager;
    /** @var KitManager */
    private KitManager $kitManager;
    /** @var KothManager */
    private KothManager $kothManager;
    /** @var ReclaimManager */
    private ReclaimManager $reclaimManager;
    /** @var SessionManager */
    private SessionManager $sessionManager;
    /** @var ShopManager */
    private ShopManager $shopManager;
    /** @var vKitManager */
    private vKitManager $vKitManager;
    
    protected function onLoad(): void
    {
        self::$instance = $this;
    }
    
    protected function onEnable() : void
    {
        if (!InvMenuHandler::isRegistered())
	        InvMenuHandler::register($this);
	
        # Register provider
        $this->provider = new Provider;
        
        # Register manager
        $this->entityManager = new EntityManager;
        $this->claimManager = new ClaimManager;
        $this->commandManager = new CommandManager;
        $this->crateManager = new CrateManager;
        $this->enchantmentManager = new EnchantmentManager;
        $this->eventManager = new EventManager;
        $this->factionManager = new FactionManager;
        $this->itemManager = new ItemManager;
        $this->kitManager = new KitManager;
        $this->kothManager = new KothManager;
        $this->reclaimManager = new ReclaimManager;
        $this->sessionManager = new SessionManager;
        $this->shopManager = new ShopManager;
        $this->vKitManager = new vKitManager;

        # Register listener
        $this->getServer()->getPluginManager()->registerEvents(new HCFListener(), $this);
        
        # Register handler
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            # Koth
            if (($kothName = $this->getKothManager()->getKothActive()) !== null) {
                if (($koth = $this->getKothManager()->getKoth($kothName)) !== null)
                    $koth->update();
                else
                    $this->getKothManager()->setKothActive(null);
            }

            # Events
            $this->getEventManager()->getSotw()->update();
            $this->getEventManager()->getEotw()->update();
                
            # Sessions
            foreach ($this->getSessionManager()->getSessions() as $session)
                $session->onUpdate();
                
            # Factions
            foreach ($this->getFactionManager()->getFactions() as $faction)
                $faction->onUpdate();
        }), 20);
    }
    
    protected function onDisable(): void
    {
        if (isset($this->provider)) $this->provider->save();
        if (isset($this->crateManager)) $this->crateManager->onDisable();
    }

    /**
     * @return HCFLoader
     */
    public static function getInstance(): HCFLoader
    {
        return self::$instance;
    }
    
    /**
     * @return Provider
     */
    public function getProvider(): Provider
    {
        return $this->provider;
    }
    
    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }
    
    /**
     * @return ClaimManager
     */
    public function getClaimManager(): ClaimManager
    {
        return $this->claimManager;
    }
    
    /**
     * @return CommandManager
     */
    public function getCommandManager(): CommandManager
    {
        return $this->commandManager;
    }
    
    /**
     * @return CrateManager
     */
    public function getCrateManager(): CrateManager
    {
        return $this->crateManager;
    }
    
    /**
     * @return EnchantmentManager
     */
    public function getEnchantmentManager(): EnchantmentManager
    {
        return $this->enchantmentManager;
    }
    
    /**
     * @return EventManager
     */
    public function getEventManager(): EventManager
    {
        return $this->eventManager;
    }
    
    /**
     * @return FactionManager
     */
    public function getFactionManager(): FactionManager
    {
        return $this->factionManager;
    }
    
    /**
     * @return ItemManager
     */
    public function getItemManager(): ItemManager
    {
        return $this->itemManager;
    }
    
    /**
     * @return KitManager
     */
    public function getKitManager(): KitManager
    {
        return $this->kitManager;
    }
    
    /**
     * @return KothManager
     */
    public function getKothManager(): KothManager
    {
        return $this->kothManager;
    }
    
    /**
     * @return ReclaimManager
     */
    public function getReclaimManager(): ReclaimManager
    {
        return $this->reclaimManager;
    }
    
    /**
     * @return SessionManager
     */
    public function getSessionManager(): SessionManager
    {
        return $this->sessionManager;
    }
    
    /**
     * @return ShopManager
     */
    public function getShopManager(): ShopManager
    {
        return $this->shopManager;
    }
    
    /**
     * @return vKitManager
     */
    public function getvKitManager(): vKitManager
    {
        return $this->vKitManager;
    }
}