<?php

declare(strict_types=1);

namespace juqn\hcf\item;

use juqn\hcf\entity\EnderpearlEntity;
use juqn\hcf\player\Player as HCFPlayer;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\item\EnderPearl as PMEnderPearl;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EnderPearlItem extends PMEnderPearl
{
    
    /**
     * EnderpearlItem construct.
     */
    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemIds::ENDER_PEARL, 0), 'Ender Pearl');
    }

    /**
     * @param Location $location
     * @param Player $thrower
     * @return Throwable
     */
    protected function createEntity(Location $location, Player $thrower): Throwable
    {
        return new EnderpearlEntity($location, $thrower);
    }
    
    /**
     * @param Player $player
     * @param Vector3 $directionVector
     * @return ItemUseResult
     */
    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult
    {
        if ($player instanceof HCFPlayer) {
            $session = $player->getSession();
            
            if ($session->getCooldown('enderpearl') !== null) {
                $player->sendMessage(TextFormat::colorize('&cYou have cooldown enderpearl'));
                return ItemUseResult::FAIL();
            }
            $result = parent::onClickAir($player, $directionVector);
            
            if ($result)
                $session->addCooldown('enderpearl', '&l&eEnderpearl: &r&7', 15);
            return $result;
        }
		return parent::onClickAir($player, $directionVector);
	}
}