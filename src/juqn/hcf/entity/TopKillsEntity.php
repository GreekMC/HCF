<?php

declare(strict_types=1);

namespace juqn\hcf\entity;

use Himbeer\LibSkin\SkinConverter;
use itoozh\Leaderboards\Leaderboards;
use JetBrains\PhpStorm\Pure;
use juqn\hcf\HCFLoader;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class TopKillsEntity extends Human
{
    
    public $canCollide = false;
    protected $gravity = 0.0;
    protected $immobile = true;

    /** @var int|null */

    /**
     * @param Player $player
     *
     * @return TopKillsEntity
     */
    public static function create(Player $player): self
    {
        $nbt = CompoundTag::create()
            ->setTag("Pos", new ListTag([
                new DoubleTag($player->getLocation()->x),
                new DoubleTag($player->getLocation()->y),
                new DoubleTag($player->getLocation()->z)
            ]))
            ->setTag("Motion", new ListTag([
                new DoubleTag($player->getMotion()->x),
                new DoubleTag($player->getMotion()->y),
                new DoubleTag($player->getMotion()->z)
            ]))
            ->setTag("Rotation", new ListTag([
                new FloatTag($player->getLocation()->yaw),
                new FloatTag($player->getLocation()->pitch)
            ]));
        return new self($player->getLocation(), $player->getSkin(), $nbt);
    }

    public function canBeMovedByCurrents(): bool
    {
        return false;
    }

    /**
     * @return array
     */
    private function getKills(): array
    {
        $kills = [];

        foreach (HCFLoader::getInstance()->getSessionManager()->getSessions() as $session) {
            $kills[$session->getName()] = $session->getKills();
        }
        return $kills;
    }

    /**
     * @param int $currentTick
     *
     * @return bool
     * @throws \JsonException
     * @throws \Exception
     */
    public function onUpdate(int $currentTick): bool
    {
        if ($currentTick % 2400 === 0) {
            $data = $this->getKills();
            arsort($data);
            
            $player = array_keys($data);
            
            if (isset($player[0])) {
                $this->setNameTagAlwaysVisible(true);
                $this->setNameTag(TextFormat::colorize('&a&l#1 Kills&r' . PHP_EOL . '&f' . $player[0] . PHP_EOL . '&o&7/leaderboards kills'));
                
                $skinData = SkinConverter::imageToSkinDataFromPngPath(HCFLoader::getInstance()->getDataFolder() . 'Skins/' . $player[0] . '.png');
                $this->setSkin(new Skin('top_kdr_skin', $skinData));
            }
        }
        $nearest = $this->location->world->getNearestEntity($this->location, 8, Player::class);
        
        if ($nearest === null)
            return parent::onUpdate($currentTick);
        $this->lookAt($nearest->getEyePos());
        return parent::onUpdate($currentTick);
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void
    {
        $source->cancel();

        if (!$source instanceof EntityDamageByEntityEvent) {
            return;
        }
        $damager = $source->getDamager();

        if (!$damager instanceof Player) {
            return;
        }

        if ($damager->getInventory()->getItemInHand()->getId() === 276) {
            if ($damager->hasPermission('god.command')) {
                $this->kill();
            }
            return;
        }

    }
}