<?php

declare(strict_types=1);

namespace juqn\hcf\entity;

use juqn\hcf\player\Player;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\color\Color;
use pocketmine\entity\effect\InstantEffect;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\item\PotionType;
use pocketmine\world\particle\PotionSplashParticle;
use pocketmine\world\sound\PotionSplashSound;

class SplashPotionEntity extends \pocketmine\entity\projectile\SplashPotion
{
    
    /** @var float */
    protected $gravity = 0.07;
    /** @var float */
    protected $drag = 0.015;
    
    /**
     * @param ProjectileHitEvent $event
     */
    protected function onHit(ProjectileHitEvent $event): void
    {
        $owning = $this->getOwningEntity();
        $effects = $this->getPotionEffects();
        $hasEffects = true;

        if (count($effects) === 0) {
            $particle = new PotionSplashParticle(PotionSplashParticle::DEFAULT_COLOR());
            $hasEffects = false;
        } else {
            $colors = [];

            foreach ($effects as $effect) {
                $level = $effect->getEffectLevel();
				
                for ($j = 0; $j < $level; ++$j)
                    $colors[] = $effect->getColor();
            }
            $particle = new PotionSplashParticle(Color::mix(...$colors));
        }
        $this->getWorld()->addParticle($this->location, $particle);
        $this->broadcastSound(new PotionSplashSound());
		
        if (!$hasEffects) {
            if ($event instanceof ProjectileHitBlockEvent and $this->getPotionType()->equals(PotionType::WATER())) {
                $blockIn = $event->getBlockHit()->getSide($event->getRayTraceResult()->getHitFace());

                if ($blockIn->getId() === BlockLegacyIds::FIRE) 
                    $this->getWorld()->setBlock($blockIn->getPosition(), VanillaBlocks::AIR());
				
                foreach ($blockIn->getHorizontalSides() as $horizontalSide) {
                    if ($horizontalSide->getId() === BlockLegacyIds::FIRE)
					    $this->getWorld()->setBlock($horizontalSide->getPosition(), VanillaBlocks::AIR());
                }
            }
            return;
	    }
		
        if ($this->willLinger())
            return;
		
        foreach ($this->getWorld()->getNearbyEntities($this->boundingBox->expandedCopy(2, 3, 2), $this) as $entity) {
            if ($entity instanceof Player and $entity->isAlive()) {
                $distanceMultiplier = 0.580 * 1.75;
				
                foreach ($this->getPotionEffects() as $effect) {
                    if (!($effect->getType() instanceof InstantEffect)) {
                        $newDuration = (int) round($effect->getDuration() * 0.75 * $distanceMultiplier);
						
                        if ($newDuration < 20)
                            continue;
                        $effect->setDuration($newDuration);
                        $entity->getEffects()->add($effect);
                    } else
                        $effect->getType()->applyEffect($entity, $effect, $distanceMultiplier, $this);
                }
            }
        }
    }
}