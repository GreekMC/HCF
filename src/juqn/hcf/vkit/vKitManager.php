<?php

declare(strict_types=1);

namespace juqn\hcf\vkit;

use juqn\hcf\HCFLoader;
use juqn\hcf\vkit\command\vKitCommand;

use pocketmine\item\Item;

/**
 * Class vKitManager
 * @package juqn\hcf\vkit
 */
class vKitManager
{
    
    /** @var vKit[] */
    private array $vKits = [];
    
    /**
     * vKitManager construct.
     */
    public function __construct()
    {
        # Register handler
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new vKitListener(), HCFLoader::getInstance());
        # Register command
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new vKitCommand());
        # Register vkits
        foreach (HCFLoader::getInstance()->getProvider()->getvKits() as $name => $data)
            $this->createvKit($name, $data['items'], false);
    }
    
    /**
     * @return vKit[]
     */
    public function getvKits(): array
    {
        return $this->vKits;
    }
    
    /**
     * @param string $vKitName
     * @return vKit
     */
    public function getvKit(string $vKitName): ?vKit
    {
        return $this->vKits[$vKitName] ?? null;
    }
    
    /**
     * @return string[]
     */
    public function getOrganization(): array
    {
        return HCFLoader::getInstance()->getProvider()->getvKitConfig()->get('organization');
    }
    
    /**
     * @param string $vKitName
     * @param Item[] $items
     * @param bool $new
     */
    public function createvKit(string $vKitName, array $items, bool $new = true): void
    {
        $this->vKits[$vKitName] = new vKit($vKitName, $items);
        
        if ($new) {
            # Organization
            $organization = $this->getOrganization();
            $organization[] = $vKitName;
            
            HCFLoader::getInstance()->getProvider()->getvKitConfig()->set('organization', $organization);
            HCFLoader::getInstance()->getProvider()->getvKitConfig()->save();
        }
    }
    
    /**
     * @param string $vKitName
     */
    public function removevKit(string $vKitName): void
    {
        unset($this->vKits[$vKitName]);
        
        # Organization
        $organization = $this->getOrganization();
        $key = array_search($vKitName, $organization);
        unset($organization[$key]);
        HCFLoader::getInstance()->getProvider()->getvKitConfig()->set('organization', $organization);
        HCFLoader::getInstance()->getProvider()->getvKitConfig()->save();
    }
    
    /**
     * @param string[] $organization
     */
    public function setOrganization(array $organization): void
    {
        HCFLoader::getInstance()->getProvider()->getvKitConfig()->set('organization', $organization);
        HCFLoader::getInstance()->getProvider()->getvKitConfig()->save();
    }
}