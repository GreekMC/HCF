<?php

declare(strict_types=1);

namespace juqn\hcf\reclaim;

use juqn\hcf\HCFLoader;
use juqn\hcf\reclaim\command\ReclaimCommand;

use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\utils\TextFormat;

/**
 * Class ReclaimManager
 * @package juqn\hcf\reclaim
 */
class ReclaimManager
{
    
    /** @var Reclaim[] */
    private array $reclaims = [];
    
    /**
     * ReclaimManager construct.
     */
    public function __construct()
    {
        # Register reclaims
        foreach (HCFLoader::getInstance()->getProvider()->getReclaims() as $name => $data) {
            $permissionManager = PermissionManager::getInstance();
            
            if ($data['permission'] !== null) {
                $this->registerPermission($data['permission']);     
                /*if ($permissionManager->getPermission($data['permission']) !== null) {
					HCFLoader::getInstance()->getLogger()->error(TextFormat::colorize('The permission of the kit ' . $name . ' already exists, the kit will not be loaded'));
					continue;
				}
				$permissionManager->addPermission(new Permission($data['permission'], 'Permission for the reclaim ' . $name));
				$permissionManager->getPermission(DefaultPermissions::ROOT_USER)->addChild($data['permission'], true);*/
            }
            $this->createReclaim($name, $data['permission'], (int) $data['time'], $data['contents']);
        }
        # Register command
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new ReclaimCommand());
    }
    
    public function registerPermission(string $permission): void {
        $manager = PermissionManager::getInstance();
        $manager->addPermission(new Permission($permission));
        $manager->getPermission(DefaultPermissions::ROOT_OPERATOR)->addChild($permission, true);
    }
    
    /**
     * @return Reclaim[]
     */
    public function getReclaims(): array
    {
        return $this->reclaims;
    }
    
    /**
     * @param string $name
     * @return Reclaim|null
     */
    public function getReclaim(string $name): ?Reclaim
    {
        return $this->reclaims[$name] ?? null;
    }
    
    /**
     * @param string $name
     * @param string $permission
     * @param int $time
     * @param Item[] $contents
     */
    public function createReclaim(string $name, string $permission, int $time, array $contents = []): void
    {
        $this->reclaims[$name] = new Reclaim($name, $permission, $time, $contents);
    }
    
    /**
     * @param string $name
     */
    public function removeReclaim(string $name): void
    {
        unset($this->reclaims[$name]);
    }
}