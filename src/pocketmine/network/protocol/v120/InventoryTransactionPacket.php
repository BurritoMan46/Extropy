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

namespace pocketmine\network\protocol\v120;

#include <rules/DataPacket.h>


use pocketmine\inventory\transactions\SimpleTransactionData;
use pocketmine\network\protocol\ContainerSetContentPacket;
use pocketmine\network\protocol\Info120;
use pocketmine\network\protocol\PEPacket;

class InventoryTransactionPacket extends PEPacket {

	const NETWORK_ID = Info120::INVENTORY_TRANSACTION_PACKET;
	const PACKET_NAME = "INVENTORY_TRANSACTION_PACKET";

	const TRANSACTION_TYPE_NORMAL = 0;
	const TRANSACTION_TYPE_INVENTORY_MISMATCH = 1;
	const TRANSACTION_TYPE_ITEM_USE = 2;
	const TRANSACTION_TYPE_ITEM_USE_ON_ENTITY = 3;
	const TRANSACTION_TYPE_ITEM_RELEASE = 4;

	const INV_SOURCE_TYPE_CONTAINER = 0;
	const INV_SOURCE_TYPE_GLOBAL = 1;
	const INV_SOURCE_TYPE_WORLD_INTERACTION = 2;
	const INV_SOURCE_TYPE_CREATIVE = 3;
	const INV_SOURCE_TYPE_CRAFT = 99999;

	const ITEM_RELEASE_ACTION_RELEASE = 0; // bow shoot
	const ITEM_RELEASE_ACTION_USE = 1; // eat food, drink potion

	const ITEM_USE_ACTION_PLACE = 0;
	const ITEM_USE_ACTION_USE = 1;
	const ITEM_USE_ACTION_DESTROY = 2;

	const ITEM_USE_ON_ENTITY_ACTION_INTERACT = 0;
	const ITEM_USE_ON_ENTITY_ACTION_ATTACK = 1;
	const ITEM_USE_ON_ENTITY_ACTION_ITEM_INTERACT = 2;

	public $transactionType;

	/** @var SimpleTransactionData */
	public $transactions;

	public $actionType;
	public $position;
	public $face;
	public $slot;
	public $item;
	public $fromPosition;
	public $clickPosition;
	public $entityId;

	public function decode(int $playerProtocol) {
		$this->getHeader($playerProtocol);
		$this->transactionType = $this->getVarInt();
		$this->transactions = $this->getTransactions($playerProtocol);
		$this->getComplexTransactions($playerProtocol);
	}

	public function encode(int $playerProtocol) {
		$this->reset($playerProtocol);
	}

	private function getTransactions(int $playerProtocol) {
		$transactions = [];
		$actionsCount = $this->getVarInt();
		for($i = 0; $i < $actionsCount; $i++) {
			$tr = new SimpleTransactionData();
			$tr->sourceType = $this->getVarInt();
			switch($tr->sourceType) {
				case self::INV_SOURCE_TYPE_CONTAINER;
					$tr->inventoryId = $this->getSignedVarInt();
					break;
				case self::INV_SOURCE_TYPE_GLOBAL: // ???
					break;
				case self::INV_SOURCE_TYPE_WORLD_INTERACTION:
					$tr->flags = $this->getVarInt(); // flags NoFlag = 0 WorldInteraction_Random = 1
					break;
				case self::INV_SOURCE_TYPE_CREATIVE:
					$tr->inventoryId = ContainerSetContentPacket::SPECIAL_CREATIVE;
					break;
				case self::INV_SOURCE_TYPE_CRAFT:
					$tr->action = $this->getVarInt();
					break;
				default:
					continue;
			}
			$tr->slot = $this->getVarInt();
			$tr->oldItem = $this->getSlot($playerProtocol);
			$tr->newItem = $this->getSlot($playerProtocol);
			$transactions[] = $tr;
		}
		return $transactions;
	}

	private function getComplexTransactions(int $playerProtocol) {
		switch($this->transactionType) {
			case self::TRANSACTION_TYPE_NORMAL:
			case self::TRANSACTION_TYPE_INVENTORY_MISMATCH:
				return;
			case self::TRANSACTION_TYPE_ITEM_USE:
				$this->actionType = $this->getVarInt();
				$this->position = [
					"x" => $this->getSignedVarInt(),
					"y" => $this->getVarInt(),
					"z" => $this->getSignedVarInt()
				];
				$this->face = $this->getSignedVarInt();
				$this->slot = $this->getSignedVarInt();
				$this->item = $this->getSlot($playerProtocol);
				$this->fromPosition = [
					"x" => $this->getLFloat(),
					"y" => $this->getLFloat(),
					"z" => $this->getLFloat()
				];
				$this->clickPosition = [
					"x" => $this->getLFloat(),
					"y" => $this->getLFloat(),
					"z" => $this->getLFloat()
				];
				return;
			case self::TRANSACTION_TYPE_ITEM_USE_ON_ENTITY:
				$this->entityId = $this->getVarInt();
				$this->actionType = $this->getVarInt();
				$this->slot = $this->getSignedVarInt();
				$this->item = $this->getSlot($playerProtocol);
				$this->fromPosition = [
					"x" => $this->getLFloat(),
					"y" => $this->getLFloat(),
					"z" => $this->getLFloat()
				];
				return;
			case self::TRANSACTION_TYPE_ITEM_RELEASE:
				$this->actionType = $this->getVarInt();
				$this->slot = $this->getSignedVarInt();
				$this->item = $this->getSlot($playerProtocol);
				$this->fromPosition = [
					"x" => $this->getLFloat(),
					"y" => $this->getLFloat(),
					"z" => $this->getLFloat()
				];
				return;
		}
	}

}