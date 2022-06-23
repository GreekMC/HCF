<?php

declare(strict_types=1);

namespace juqn\hcf\provider;

use juqn\hcf\HCFLoader;
use pocketmine\item\Item;
use pocketmine\utils\Config;

/**
 * Class Provider
 * @package juqn\hcf\provider
 */
class Provider
{
    
    /** @var Config */
    private Config $crateConfig, $kothConfig, $kitConfig, $reclaimConfig, $shopConfig, $vKitConfig;
    
    /**
     * YamlProvider construct
     */
    public function __construct()
    {
        $plugin = HCFLoader::getInstance();
        
        # Creation of folders that do not exist
        if (!is_dir($plugin->getDataFolder() . 'database'))
            @mkdir($plugin->getDataFolder() . 'database');
        
        if (!is_dir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'players'))
            @mkdir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'players');
            
        if (!is_dir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'factions'))
            @mkdir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'factions');
        
        # Save default config
        $plugin->saveDefaultConfig();
           
        # Creation configs and save variables
        $this->crateConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'crates.json', Config::JSON);
        $this->kothConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'koths.json', Config::JSON);
        $this->kitConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'kits.json', Config::JSON, [
            'organization' => [],
            'kits' => []
        ]);
        $this->reclaimConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'reclaims.json', Config::JSON);
        $this->shopConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'shops.json', Config::JSON);
        $this->vKitConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'vkits.json', Config::JSON, [
            'organization' => [],
            'vkits' => []
        ]);
    }
    
    public function save(): void
    {
        $this->savePlayers();
        $this->saveFactions();
        $this->saveCrates();
        $this->saveKoths();
        $this->saveKits();
        $this->saveReclaims();
        $this->saveShops();
        $this->savevKits();
    }
    
    /**
     * @return Config
     */
    public function getCrateConfig(): Config
    {
        return $this->crateConfig;
    }
    
    /**
     * @return Config
     */
    public function getKothConfig(): Config
    {
        return $this->kothConfig;
    }
    
    /**
     * @return Config
     */
    public function getKitConfig(): Config
    {
        return $this->kitConfig;
    }
    
    /**
     * @return Config
     */
    public function getReclaimConfig(): Config
    {
        return $this->reclaimConfig;
    }
    
    /**
     * @return Config
     */
    public function getShopConfig(): Config
    {
        return $this->shopConfig;
    }
    
    /**
     * @return Config
     */
    public function getvKitConfig(): Config
    {
        return $this->vKitConfig;
    }
    
    /**
     * @return array
     */
    public function getPlayers(): array
    {
        $players = [];
        
        foreach (glob(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'players' . DIRECTORY_SEPARATOR . '*.json') as $file)
            $players[basename($file, '.json')] = (new Config(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'players' . DIRECTORY_SEPARATOR . basename($file), Config::JSON))->getAll();
        return $players;
    }
    
    /**
     * @return array
     */
    public function getFactions(): array
    {
        $factions = [];
        
        foreach (glob(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'factions' . DIRECTORY_SEPARATOR . '*.json') as $file)
            $factions[basename($file, '.json')] = (new Config(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'factions' . DIRECTORY_SEPARATOR . basename($file), Config::JSON))->getAll();
        return $factions;
    }
    
    /**
     * @return array
     */
    public function getCrates(): array
    {
        $crates = [];
        
        foreach ($this->crateConfig->getAll() as $name => $data) {
            if (isset($data['items'])) {
                foreach ($data['items'] as $slot => $item)
                $data['items'][$slot] = Item::jsonDeserialize($item);
            }
            $crates[$name] = $data;
        }
        return $crates;
    }
    
    /**
     * @return array
     */
    public function getKoths(): array
    {
        $koths = [];
        
        foreach ($this->kothConfig->getAll() as $name => $data) {
            $koths[$name] = $data;
        }
        return $koths;
    }
    
    /**
     * @return array
     */
    public function getKits(): array
    {
        $kits = [];
        
        foreach ($this->kitConfig->get('kits') as $name => $data) {
            if ($data['representativeItem'] !== null)
                $data['representativeItem'] = Item::jsonDeserialize($data['representativeItem']);
            
            if (isset($data['items'])) {
                foreach ($data['items'] as $slot => $item)
                    $data['items'][$slot] = Item::jsonDeserialize($item);
            }
             
            if (isset($data['armor'])) {   
                foreach ($data['armor'] as $slot => $armor)
                    $data['armor'][$slot] = Item::jsonDeserialize($armor);
            }
            $kits[$name] = $data;
        }
        return $kits;
    }
    
    /**
     * @return array
     */
    public function getReclaims(): array
    {
        $reclaims = [];
        
        foreach ($this->reclaimConfig->getAll() as $name => $data) {
            if ($data['contents'] !== null) {
                foreach ($data['contents'] as $item)
                    $data['contents'][] = Item::jsonDeserialize($item);
            }
            $reclaims[$name] = $data;
        }
        return $reclaims;
    }
    
    /**
     * @return array
     */
    public function getShops(): array
    {
        $shops = [];
        
        foreach ($this->shopConfig->getAll() as $name => $data) {
            $data['item'] = Item::jsonDeserialize($data['item']);
            $shops[$name] = $data;
        }
        return $shops;
    }
    
    /**
     * @return array
     */
    public function getvKits(): array
    {
        $vkits = [];
        
        foreach ($this->vKitConfig->get('vkits') as $name => $data) {
            if (isset($data['items'])) {
                foreach ($data['items'] as $slot => $item)
                    $data['items'][$slot] = Item::jsonDeserialize($item);
            } else $data['items'] = [];
            $vkits[$name] = $data;
        }
        return $vkits;
    }
    
    public function savePlayers(): void
    {
        foreach (HCFLoader::getInstance()->getSessionManager()->getSessions() as $xuid => $session) {
            $config = new Config(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'players' . DIRECTORY_SEPARATOR  . $xuid . '.json', Config::JSON);
            $config->setAll($session->getData());
            $config->save();
        }
    }
    
    public function saveFactions(): void
    {
        foreach (HCFLoader::getInstance()->getFactionManager()->getFactions() as $name => $faction) {
            $config = new Config(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'factions' . DIRECTORY_SEPARATOR . $name . '.json', Config::JSON);
            $config->setAll($faction->getData());
            $config->save();
        }
    }
    
    public function saveCrates(): void
    {
        $crates = [];
        
        foreach (HCFLoader::getInstance()->getCrateManager()->getCrates() as $crate) {
            $crates[$crate->getName()] = $crate->getData();
        }
        $this->crateConfig->setAll($crates);
        $this->crateConfig->save();
    }
    
    public function saveKoths(): void
    {
        $koths = [];
        
        foreach (HCFLoader::getInstance()->getKothManager()->getKoths() as $koth) {
            $koths[$koth->getName()] = $koth->getData();
        }
        $this->kothConfig->setAll($koths);
        $this->kothConfig->save();
    }
    
    public function saveKits(): void
    {
        $kits = [];
        
        foreach (HCFLoader::getInstance()->getKitManager()->getKits() as $kit) {
            $kits[$kit->getName()] = $kit->getData();
        }
        $this->kitConfig->set('kits', $kits);
        $this->kitConfig->save();
    }
    
    public function saveReclaims(): void
    {
        $reclaims = [];
        
        foreach (HCFLoader::getInstance()->getReclaimManager()->getReclaims() as $reclaim) {
            $reclaims[$reclaim->getName()] = $reclaim->getData();
        }
        $this->reclaimConfig->setAll($reclaims);
        $this->reclaimConfig->save();
    }
    
    public function saveShops(): void
    {
        $shops = [];
        
        foreach (HCFLoader::getInstance()->getShopManager()->getShops() as $location => $shop) {
            $shops[$location] = $shop->getData();
        }
        $this->shopConfig->setAll($shops);
        $this->shopConfig->save();
    }
    
    public function savevKits(): void
    {
        $vkits = [];
        
        foreach (HCFLoader::getInstance()->getvKitManager()->getvKits() as $vkit) {
            $vkits[$vkit->getName()] = $vkit->getData();
        }
        $this->vKitConfig->set('vkits', $vkits);
        $this->vKitConfig->save();
    }
}