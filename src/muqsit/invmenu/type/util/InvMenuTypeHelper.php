<?php

declare(strict_types=1);

namespace muqsit\invmenu\type\util;

use pocketmine\math\Vector3;

final class InvMenuTypeHelper{

	public const NETWORK_WORLD_Y_MIN = -64;
	public const NETWORK_WORLD_Y_MAX = 320;

	public static function getPositionOffset() : Vector3{
		return new Vector3(0, -2, 0);
	}

	public static function isValidYCoordinate(float $y) : bool{
		return $y >= self::NETWORK_WORLD_Y_MIN && $y <= self::NETWORK_WORLD_Y_MAX;
	}
}