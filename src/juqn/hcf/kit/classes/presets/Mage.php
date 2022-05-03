<?php

declare(strict_types=1);

namespace juqn\hcf\kit\classes\presets;

use juqn\hcf\kit\classes\ClassFactory;
use juqn\hcf\kit\classes\HCFClass;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

/**
 * Class Mage
 * @package juqn\hcf\kit\classes\presets
 */
class Mage extends HCFClass
{

    /**
     * Mage construct.
     */
    public function __construct()
    {
        parent::__construct(self::MAGE);
    }

    /**
     * @return Item[]
     */
    public function getArmorItems(): array
    {
        return [
            VanillaItems::GOLDEN_HELMET(),
            VanillaItems::CHAINMAIL_CHESTPLATE(),
            VanillaItems::CHAINMAIL_LEGGINGS(),
            VanillaItems::GOLDEN_BOOTS()
        ];
    }

    /**
     * @return EffectInstance[]
     */
    public function getEffects(): array
    {
        return ClassFactory::getClassById(self::ARCHER)->getEffects();
    }
}