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

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\Player;
use pocketmine\utils\Random;

class TNT extends Solid {

	protected $id = self::TNT;

	public function __construct(int $meta = 0) {
		$this->meta = $meta;
	}

	public function getName() : string {
		return "TNT";
	}

	public function willDamageTools() : bool {
		return false;
	}

	public function getHardness() : float {
		return 0;
	}

	public function onActivate(Item $item, Player $player = null) : bool {
		if($item->getId() === Item::FLINT_STEEL) {
			$this->ignite();
			return true;
		}
		return false;
	}

	public function ignite(int $fuse = 80) {
		$this->getLevel()->setBlock($this, BlockFactory::get(Block::AIR), true);
		$mot = (new Random())->nextSignedFloat() * M_PI * 2;
		$tnt = Entity::createEntity("PrimedTNT", $this->getLevel()->getChunk($this->x >> 4, $this->z >> 4), new Compound("", [
			new Enum("Pos", [
				new DoubleTag("", $this->x + 0.5),
				new DoubleTag("", $this->y),
				new DoubleTag("", $this->z + 0.5),
			]),
			new Enum("Motion", [
				new DoubleTag("", -sin($mot) * 0.02),
				new DoubleTag("", 0.2),
				new DoubleTag("", -cos($mot) * 0.02),
			]),
			new Enum("Rotation", [
				new FloatTag("", 0),
				new FloatTag("", 0),
			]),
			new ByteTag("Fuse", $fuse),
		]));
		$tnt->spawnToAll();
	}

}