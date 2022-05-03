<?php

declare(strict_types=1);

namespace juqn\hcf\kit\classes\presets;

use juqn\hcf\kit\classes\HCFClass;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

/**
 * Class Miner
 * @package juqn\hcf\kit\classes\presets
 */
class Miner extends HCFClass
{

    /**
     * Miner construct.
     */
    public function __construct()
    {
        parent::__construct(self::MINER);
    }

    /**
     * @return Item[]
     */
    public function getArmorItems(): array
    {
        return [
            VanillaItems::IRON_HELMET(),
            VanillaItems::IRON_CHESTPLATE(),
            VanillaItems::IRON_LEGGINGS(),
            VanillaItems::IRON_BOOTS()
        ];
    }

    /**
     * @return EffectInstance[]
     */
    public function getEffects(): array
    {
        return [
            new EffectInstance(VanillaEffects::HASTE(), 20 * 15, 2),
            new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 20 * 15, 1),
            new EffectInstance(VanillaEffects::NIGHT_VISION(), 20 * 15, 1)
        ];
    }
}