<?php

declare(strict_types=1);

namespace juqn\hcf\entity;

use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Cobweb;
use pocketmine\block\FenceGate;
use pocketmine\block\Slab;
use pocketmine\block\Stair;
use pocketmine\block\Wall;
use pocketmine\entity\projectile\EnderPearl;
use pocketmine\math\Facing;
use pocketmine\math\RayTraceResult;
use pocketmine\math\Vector3;

class EnderPearlEntity extends EnderPearl {

    protected $gravity = 0.03, $drag = 0.01;

    private const PRESSURE_PLATES = [
        BlockLegacyIds::WOODEN_PRESSURE_PLATE,
        BlockLegacyIds::STONE_PRESSURE_PLATE,
        BlockLegacyIds::LIGHT_WEIGHTED_PRESSURE_PLATE,
        BlockLegacyIds::HEAVY_WEIGHTED_PRESSURE_PLATE
    ];
    private bool $alreadyPassed = false;

    protected function calculateInterceptWithBlock(Block $block, Vector3 $start, Vector3 $end): ?RayTraceResult {
        if(self::canPassThrough($block) && !$this->alreadyPassed){
            $this->alreadyPassed = true;
            return null;
        }

        return $block->calculateIntercept($start, $end);
    }

    public static function canPassThrough(Block $block):bool {
        if($block instanceof FenceGate && $block->isOpen())return true;
        if($block instanceof Cobweb)return true;
        if($block instanceof Slab)return true;
        if($block instanceof Stair)return true;
        if($block instanceof Wall)return true;
        if($block instanceof Air && count($block->getSide(Facing::UP)->getCollisionBoxes()) > 0 && count($block->getSide(Facing::DOWN)->getCollisionBoxes()) > 0)return true;
        if(in_array($block->getId(), self::PRESSURE_PLATES))return true;
        return false;
    }

    public static function canPass(Block $block):bool {
        if($block instanceof Air && count($block->getSide(Facing::UP)->getCollisionBoxes()) > 0 && count($block->getSide(Facing::DOWN)->getCollisionBoxes()) > 0)return true;
        return false;
    }
}