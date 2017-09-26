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

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\tool\pickaxe\Pickaxe;
use pocketmine\item\tool\Tool;
use pocketmine\item\tool\ToolTier;

class LapisOre extends Solid {

	protected $id = self::LAPIS_ORE;

	public function __construct(int $meta = 0) {
		$this->meta = $meta;
	}

	public function getHardness() : float {
		return 3;
	}

	public function getToolType() : int {
		return Tool::TYPE_PICKAXE;
	}

	public function getName() : string {
		return "Lapis Lazuli Ore";
	}

	public function getDrops(Item $item) : array {
		if($item instanceof Pickaxe and $item->getTier() >= ToolTier::TIER_STONE) {
			return [
				ItemFactory::get(Item::DYE, 4, mt_rand(4, 8)),
			];
		}
		return [];
	}

}