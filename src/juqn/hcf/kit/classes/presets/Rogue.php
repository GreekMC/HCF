<?php

declare(strict_types=1);

namespace juqn\hcf\kit\classes\presets;

use juqn\hcf\kit\classes\ClassFactory;
use juqn\hcf\kit\classes\HCFClass;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

/**
 * Class Rogue
 * @package juqn\hcf\kit\classes\presets
 */
class Rogue extends HCFClass
{

    /**
     * Rogue construct.
     */
    public function __construct()
    {
        parent::__construct(self::ROGUE);
    }

    /**
     * @return Item[]
     */
    public function getArmorItems(): array
    {
        return [
            VanillaItems::CHAINMAIL_HELMET(),
            VanillaItems::CHAINMAIL_CHESTPLATE(),
            VanillaItems::CHAINMAIL_LEGGINGS(),
            VanillaItems::CHAINMAIL_BOOTS()
        ];
    }

    /**
     * @return EffectInstance[]
     */
    public function getEffects(): array
    {
        return array_merge(
            ClassFactory::getClassById(self::ARCHER)->getEffects(),
            [new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 15)]
        );
    }
}