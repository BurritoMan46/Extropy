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

namespace pocketmine\network\protocol;

#include <rules/DataPacket.h>


class ResourcePackDataInfoPacket extends PEPacket {

	const NETWORK_ID = Info::RESOURCE_PACK_DATA_INFO_PACKET;
	const PACKET_NAME = "RESOURCE_PACK_DATA_INFO_PACKET";

	public function decode(int $playerProtocol) {

	}

	public function encode(int $playerProtocol) {
		$this->reset($playerProtocol);
		$this->putString('53644fac-a276-42e5-843f-a3c6f169a9ab'); // pack id
		$this->putInt(1); // size
		$this->putInt(0); // chunk count
		$this->putLong(1); // compressed size
		$this->putString('resources'); // sha256
	}

}