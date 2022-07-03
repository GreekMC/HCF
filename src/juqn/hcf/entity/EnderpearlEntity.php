<?php

declare(strict_types=1);

namespace juqn\hcf\entity;

use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\FenceGate;
use pocketmine\block\Slab;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\projectile\Throwable;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\RayTraceResult;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\timings\Timings;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\sound\EndermanTeleportSound;

class EnderpearlEntity extends Throwable
{
    
	protected Vector3 $position;

	protected $gravity = 0.03, $drag = 0.01;

	public static function getNetworkTypeId(): string
	{
		return EntityIds::ENDER_PEARL;
	}

	public function onUpdate(int $currentTick): bool
	{
		if ($this->closed) {
			return false;
		}

		$this->readPosition();

		$tickDiff = $currentTick - $this->lastUpdate;
		if ($tickDiff <= 0) {
			if (!$this->justCreated) {
				$this->server->getLogger()->debug("Expected tick difference of at least 1, got $tickDiff for " . get_class($this));
			}

			return true;
		}

		$this->lastUpdate = $currentTick;

		if (!$this->isAlive()) {
			if ($this->onDeathUpdate($tickDiff)) {
				$this->flagForDespawn();
			}

			return true;
		}

		$this->timings->startTiming();

		if ($this->hasMovementUpdate()) {
			$this->tryChangeMovement();

			if (abs($this->motion->x) <= self::MOTION_THRESHOLD) {
				$this->motion->x = 0;
			}
			if (abs($this->motion->y) <= self::MOTION_THRESHOLD) {
				$this->motion->y = 0;
			}
			if (abs($this->motion->z) <= self::MOTION_THRESHOLD) {
				$this->motion->z = 0;
			}

			if ($this->motion->x !== 0 || $this->motion->y !== 0 || $this->motion->z !== 0) {
				$this->move($this->motion->x, $this->motion->y, $this->motion->z);
			}

			$this->forceMovementUpdate = false;
		}

		$this->updateMovement();

		Timings::$entityBaseTick->startTiming();
		$hasUpdate = $this->entityBaseTick($tickDiff);
		Timings::$entityBaseTick->stopTiming();

		$this->timings->stopTiming();

		return ($hasUpdate or $this->hasMovementUpdate());
	}

	/**
	 * @return void
	 */
	protected function readPosition(): void
	{
		$new = $this->getPosition();
		if ($new->distanceSquared($this->getPositionPlayer()) > 1) {
			$this->setPositionPlayer(new Vector3($this->getPosition()->getX(), (int)$this->getPosition()->getY(), $this->getPosition()->getZ()));
		}
	}

	protected function getPositionPlayer(): Vector3
	{
		return $this->position ?? new Vector3(0, 0, 0);
	}

	/**
	 * @param Vector3 $position
	 */
	protected function setPositionPlayer(Vector3 $position): void
	{
		$this->position = $position;
	}

	protected function getInitialSizeInfo(): EntitySizeInfo
	{
		return new EntitySizeInfo(0.8, 0.8, 0.8);
	}

	protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult): void
	{
		if ($this->getOwningEntity() !== null) {
			$this->getOwningEntity()->teleport($entityHit->getPosition());
			$this->getOwningEntity()->attack(new EntityDamageEvent($this->getOwningEntity(), EntityDamageEvent::CAUSE_FALL, 2));
			$this->getWorld()->addSound($this->getOwningEntity()->getPosition(), new EndermanTeleportSound());
		}
		$this->flagForDespawn();
	}

	protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult): void
	{
		if ($this->getOwningEntity()) {
			$blocksAllowed = [
				BlockLegacyIds::CHEST,
				BlockLegacyIds::ENDER_CHEST,
				BlockLegacyIds::ANVIL,
				BlockLegacyIds::COBBLESTONE_WALL,
				BlockLegacyIds::STONE_BRICK_STAIRS,
				BlockLegacyIds::ACACIA_STAIRS,
				BlockLegacyIds::STONE_STAIRS,
				BlockLegacyIds::BIRCH_STAIRS,
				BlockLegacyIds::BRICK_STAIRS,
				BlockLegacyIds::SANDSTONE_STAIRS,
				BlockLegacyIds::COBBLESTONE_STAIRS,
				BlockLegacyIds::DARK_OAK_STAIRS,
				BlockLegacyIds::JUNGLE_STAIRS,
				BlockLegacyIds::QUARTZ_STAIRS,
				BlockLegacyIds::OAK_STAIRS,
				BlockLegacyIds::END_PORTAL,
				BlockLegacyIds::WOODEN_SLAB,
				BlockLegacyIds::STONE_SLAB,
				BlockLegacyIds::ENCHANTING_TABLE
			];

			if (in_array($blockHit->getId(), $blocksAllowed, true)) {
				$player = $this->getOwningEntity();
				if (!$player instanceof Player) {
					return;
				}
				$i = $this->getDirection($player);
				if ($i === 0) {
					$blockBack = $blockHit->getPosition()->getWorld()->getBlockAt($blockHit->getPosition()->getFloorX() + 1, $blockHit->getPosition()->getFloorY(), $blockHit->getPosition()->getFloorZ());
				} elseif ($i === 1) {
					$blockBack = $blockHit->getPosition()->getWorld()->getBlockAt($blockHit->getPosition()->getFloorX(), $blockHit->getPosition()->getFloorY(), $blockHit->getPosition()->getFloorZ() + 1);
				} elseif ($i === 2) {
					$blockBack = $blockHit->getPosition()->getWorld()->getBlockAt($blockHit->getPosition()->getFloorX() - 1, $blockHit->getPosition()->getFloorY(), $blockHit->getPosition()->getFloorZ());
				} elseif ($i === 3) {
					$blockBack = $blockHit->getPosition()->getWorld()->getBlockAt($blockHit->getPosition()->getFloorX(), $blockHit->getPosition()->getFloorY(), $blockHit->getPosition()->getFloorZ() - 1);
				}

				if (!isset($blockBack)) {
					$this->flagForDespawn();
					return;
				}

				if ($blockBack->getId() === BlockLegacyIds::AIR && $blockBack->getPosition()->getWorld()->getBlock($blockBack->getPosition()->add(0, 1, 0))->getId() === BlockLegacyIds::AIR) {
					$this->getWorld()->addSound($this->getOwningEntity()->getPosition(), new EndermanTeleportSound());
					$this->getOwningEntity()->teleport($blockBack->getPosition());
					$this->flagForDespawn();
				} else {
					$this->teleportAt();
				}
			} else {
				$this->teleportAt();
			}
		} else {
			$this->flagForDespawn();
		}
	}

	public function getDirection(Player $player): ?int
	{
		$rotation = fmod($player->getLocation()->getYaw() - 90, 360);
		if ($rotation < 0) {
			$rotation += 360.0;
		}
		if ((0 <= $rotation && $rotation < 45) || (315 <= $rotation && $rotation < 360)) {
			return 2; //North
		}

		if (45 <= $rotation && $rotation < 135) {
			return 3; //East
		}

		if (135 <= $rotation && $rotation < 225) {
			return 0; //South
		}

		if (225 <= $rotation && $rotation < 315) {
			return 1; //West
		}

		return null;
	}

	/**
	 * @return void
	 */
	protected function teleportAt(): void
	{
		if (!$this->getOwningEntity() instanceof Player || !$this->getOwningEntity()->isOnline()) {
			$this->flagForDespawn();
			return;
		}
		if ($this->getOwningEntity() instanceof Player && $this->isFence()) {
			$this->flagForDespawn();
			$this->getOwningEntity()->sendTip(TextFormat::YELLOW . "Your EnderPearl was returned, to avoid glitching");
			return;
		}

		if ($this->getPosition()->getY() > 0) {
			if ($this->isPearling()) {
				$direction = $this->getOwningEntity()->getDirectionVector()->multiply(3);
				$this->getWorld()->addSound($this->getOwningEntity()->getPosition(), new EndermanTeleportSound());

				$this->getOwningEntity()->teleport(Position::fromObject($this->getOwningEntity()->getPosition()->add($direction->x, (int)$direction->y + 1, $direction->z), $this->getOwningEntity()->getWorld()));
				$this->getOwningEntity()->attack(new EntityDamageEvent($this->getOwningEntity(), EntityDamageEvent::CAUSE_FALL, 2));

				$this->getWorld()->addSound($this->getOwningEntity()->getPosition(), new EndermanTeleportSound());
				$this->flagForDespawn();
				return;
			}

			$this->getWorld()->addSound($this->getOwningEntity()->getPosition(), new EndermanTeleportSound());

			$this->getOwningEntity()->setPosition($this->getPositionPlayer());
			$this->getOwningEntity()->teleport($this->getPositionPlayer());
			$this->getOwningEntity()->attack(new EntityDamageEvent($this->getOwningEntity(), EntityDamageEvent::CAUSE_FALL, 2));

			$this->getWorld()->addSound($this->getOwningEntity()->getPosition(), new EndermanTeleportSound());

		}
		$this->flagForDespawn();
	}

	public function isFence(): bool
	{
		for ($x = ((int)$this->getPosition()->getX()); $x <= ((int)$this->getPosition()->getX()); $x++) {
			for ($z = ((int)$this->getPosition()->getZ()); $z <= ((int)$this->getPosition()->getZ()); $z++) {
				$block = $this->getWorld()->getBlockAt($x, (int)$this->getPosition()->getY(), $z);
				if ($block instanceof FenceGate) {
					return true;
				}
			}
		}
		return false;
	}

	public function isPearling(): bool
	{
		for ($x = ($this->getPosition()->getX() + 0.1); $x <= ($this->getPosition()->getX() - 0.1); $x++) {
			for ($z = ($this->getPosition()->getZ() + 0.1); $z <= ($this->getPosition()->getZ() - 0.1); $z++) {
				$block = $this->getWorld()->getBlockAt((int)$x, $this->getPosition()->getY(), (int)$z);
				if ($block instanceof Slab) {
					return true;
				}
			}
		}
		return false;
	}
}