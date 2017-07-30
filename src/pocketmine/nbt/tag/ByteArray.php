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

namespace pocketmine\nbt\tag;

use pocketmine\nbt\NBT;
use pocketmine\utils\Binary;

#include <rules/NBT.h>

class ByteArray extends NamedTag{

	public function getType(){
		return NBT::TAG_ByteArray;
	}

	public function read(NBT $nbt){
		$this->value = $nbt->get($nbt->endianness === 1 ? Binary::readInt($nbt->get(4)) : Binary::readLInt($nbt->get(4)));
	}

	public function write(NBT $nbt){
		$nbt->buffer .= $nbt->endianness === 1 ? pack("N", strlen($this->value)) : pack("V", strlen($this->value));
		$nbt->buffer .= $this->value;
	}

	/**
	 * @param string $value
	 *
	 * @throws \TypeError
	 */
	public function setValue($value){
		if(!is_string($value)){
			throw new \TypeError("ByteArray value must be of type string, " . gettype($value) . " given");
		}
		parent::setValue($value);
	}
}