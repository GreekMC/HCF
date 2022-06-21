<?php

declare(strict_types=1);

namespace juqn\hcf\kit;

use juqn\hcf\HCFLoader;
use juqn\hcf\kit\classes\ClassFactory;
use juqn\hcf\kit\command\KitCommand;

use pocketmine\item\Item;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

/**
 * Class KitManager
 * @package juqn\hcf\kit
 */
class KitManager
{
    
    /** @var Kit[] */
    private array $kits = [];
    
    /**
     * KitManager construct.
     */
    public function __construct()
    {
        # Register command
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new KitCommand());
        # Register kits
        foreach (HCFLoader::getInstance()->getProvider()->getKits() as $name => $data) {
            $permissionManager = PermissionManager::getInstance();
            
            if ($data['permission'] !== null) {         
                if ($permissionManager->getPermission($data['permission']) !== null) {
					HCFLoader::getInstance()->getLogger()->error(TextFormat::colorize('The permission of the kit ' . $name . ' already exists, the kit will not be loaded'));
					continue;
				}
				$permissionManager->addPermission(new Permission($data['permission'], 'Permission for the kit ' . $name));
				$permissionManager->getPermission(DefaultPermissions::ROOT_USER)->addChild($data['permission'], true);
            }
            $this->addKit($name, $data['nameFormat'], $data['permission'], $data['representativeItem'], $data['items'] ?? [], $data['armor'] ?? [], $data['cooldown'] ?? 0, false);
        }
        # Register classes
        ClassFactory::init();
    }
    
    /**
     * @return Kit[]
     */
    public function getKits(): array
    {
        return $this->kits;
    }
    
    /**
     * @return string[]
     */
    public function getOrganization(): array
    {
        return HCFLoader::getInstance()->getProvider()->getKitConfig()->get('organization');
    }
    
    /**
     * @param string $kitName
     * @return Kit|null
     */
    public function getKit(string $kitName): ?Kit
    {
        return $this->kits[$kitName] ?? null;
    }
    
    /**
     * @param string $kitName
     * @param string $nameFormat
     * @param string|null $permission
     * @param Item|null $itemRepresentative
     * @param Item[] $items
     * @param Item[] $armor
     * @param int $cooldown
     * @param bool $new
     */
    public function addKit(string $kitName, string $nameFormat, ?string $permission, ?Item $itemRepresentative, array $items, array $armor, int $cooldown, bool $new = true): void
    {
        $this->kits[$kitName] = new Kit($kitName, $nameFormat, $permission, $itemRepresentative, $items, $armor, $cooldown);
        
        if ($new) {
            # Organization
            $organization = $this->getOrganization();
            $organization[] = $kitName;
            HCFLoader::getInstance()->getProvider()->getKitConfig()->set('organization', $organization);
            HCFLoader::getInstance()->getProvider()->getKitConfig()->save();
        }
    }

    /**
     * @param string $kitName
     * @throws \JsonException
     */
    public function removeKit(string $kitName): void
    {
        unset($this->kits[$kitName]);
        
        # Organization
        $organization = $this->getOrganization();
        $key = array_search($kitName, $organization);
        unset($organization[$key]);
        HCFLoader::getInstance()->getProvider()->getKitConfig()->set('organization', $organization);
        HCFLoader::getInstance()->getProvider()->getKitConfig()->save();
    }

    /**
     * @param string[] $organization
     * @throws \JsonException
     */
    public function setOrganization(array $organization): void
    {
        HCFLoader::getInstance()->getProvider()->getKitConfig()->set('organization', $organization);
        HCFLoader::getInstance()->getProvider()->getKitConfig()->save();
    }
}