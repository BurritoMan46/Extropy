<?php

namespace pocketmine\network\protocol\v120;

use pocketmine\network\protocol\Info120;
use pocketmine\network\protocol\PEPacket;

class PlayerSkinPacket extends PEPacket {

	const NETWORK_ID = Info120::PLAYER_SKIN_PACKET;
	const PACKET_NAME = "PLAYER_SKIN_PACKET";

	public $uuid;
	public $newSkinId;
	public $newSkinName;
	public $oldSkinName;
	public $newSkinByteData;
	public $newCapeByteData;
	public $newSkinGeometryName;
	public $newSkinGeometryData;


	public function decode($playerProtocol) {
		$this->uuid = $this->getUUID();
		$this->newSkinId = $this->getString();
		$this->newSkinName = $this->getString();
		$this->oldSkinName = $this->getString();
		$this->newSkinByteData = $this->getString();
		$this->newCapeByteData = $this->getString();
		$this->newSkinGeometryName = $this->getString();
		$this->newSkinGeometryData = $this->getString();
	}

	public function encode($playerProtocol) {
	}
}