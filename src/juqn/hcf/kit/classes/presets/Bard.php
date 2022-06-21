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
class Bard extends HCFClass
{

    /**
     * Bard construct.
     */
    public function __construct()
    {
        parent::__construct(self::BARD);
    }

    /**
     * @return Item[]
     */
    public function getArmorItems(): array
    {
        return [
            VanillaItems::GOLDEN_HELMET(),
            VanillaItems::GOLDEN_CHESTPLATE(),
            VanillaItems::GOLDEN_LEGGINGS(),
            VanillaItems::GOLDEN_BOOTS()
        ];
    }

    /**
     * @return EffectInstance[]
     */
    public function getEffects(): array
    {
        return [
            new EffectInstance(VanillaEffects::SPEED(), 20 * 15, 1),
            new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 15, 0),
            new EffectInstance(VanillaEffects::REGENERATION(), 20 * 15, 0)
        ];
    }
}