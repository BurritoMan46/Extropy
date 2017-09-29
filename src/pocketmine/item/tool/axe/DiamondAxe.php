<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

namespace pocketmine\item\tool\axe;

use pocketmine\item\tool\ToolTier;

class DiamondAxe extends Axe {

	public function __construct(int $meta = 0){
		parent::__construct(self::DIAMOND_AXE, $meta, "Diamond Axe");
	}

	public function getAttackPoints() : int {
		return 6;
	}

	public function getMaxDurability() : int {
		return 1562;
	}

	public function getTier() : int {
		return ToolTier::TIER_DIAMOND;
	}

}