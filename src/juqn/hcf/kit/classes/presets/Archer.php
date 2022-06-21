<?php

declare(strict_types=1);

namespace juqn\hcf\kit\classes\presets;

use juqn\hcf\kit\classes\HCFClass;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

/**
 * Class Archer
 * @package juqn\hcf\kit\classes\presets
 */
class Archer extends HCFClass
{
    
    /**
     * Archer construct.
     */
    public function __construct()
    {
        parent::__construct(self::ARCHER);
    }
    
    /**
     * @return Item[]
     */
    public function getArmorItems(): array
    {
        return [
            VanillaItems::LEATHER_CAP(),
            VanillaItems::LEATHER_TUNIC(),
            VanillaItems::LEATHER_PANTS(),
            VanillaItems::LEATHER_BOOTS()
        ];
    }

    
    /**
     * @return EffectInstance[]
     */
    public function getEffects(): array
    {
        return [
            new EffectInstance(VanillaEffects::SPEED(), 20 * 15, 2),
            new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 15, 1),
            new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 20 * 15, 0)
        ];
    }
}