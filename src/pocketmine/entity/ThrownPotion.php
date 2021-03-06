<?php

/**
 *
 *  _____      _
 * | ____|_  _| |_ _ __ ___  _ __  _   _
 * |  _| \ \/ / __| '__/ _ \| '_ \| | | |
 * | |___ >  <| |_| | | (_) | |_) | |_| |
 * |_____/_/\_\\__|_|  \___/| .__/ \__, |
 *                          |_|    |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Jack Noordhuis
 * @link   https://github.com/CrazedCraft/Extropy
 *
 *
 */

namespace pocketmine\entity;

use pocketmine\item\food\Potion;
use pocketmine\level\format\FullChunk;
use pocketmine\level\particle\SpellParticle;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;

class ThrownPotion extends Projectile {

	const NETWORK_ID = 86;

	const DATA_POTION_ID = 16;

	public $width = 0.25;
	public $length = 0.25;
	public $height = 0.25;
	protected $gravity = 0.1;
	protected $drag = 0.05;

	public function __construct(FullChunk $chunk, Compound $nbt, Entity $shootingEntity = null) {
		if(!isset($nbt->PotionId)) {
			$nbt->PotionId = new ShortTag("PotionId", Potion::AWKWARD);
		}
		parent::__construct($chunk, $nbt, $shootingEntity);
		unset($this->dataProperties[self::DATA_SHOOTER_ID]);
		$this->setDataProperty(self::DATA_POTION_AUX_VALUE, self::DATA_TYPE_SHORT, $this->getPotionId());
	}

	public function getPotionId() : int {
		return (int) $this->namedtag["PotionId"];
	}

	public function onUpdate($currentTick) {
		if($this->closed) {
			return false;
		}
		$hasUpdate = parent::onUpdate($currentTick);
		$this->age++;
		if($this->age > 1200 or $this->isCollided) {
			$this->kill();
			$this->close();
			$hasUpdate = true;
		}
		if($this->onGround) {
			$this->kill();
			$this->close();
		}
		return $hasUpdate;
	}

	public function kill() {
		$this->getLevel()->addParticle(new SpellParticle($this, ...Potion::getColor($this->getPotionId())));
		$this->setGenericFlag(self::DATA_FLAG_HAS_COLLISION, true);
		$this->getLevel()->broadcastLevelSoundEvent($this->asVector3(), 116);
		foreach($this->getViewers() as $p) {
			if($p->distance($this) <= 6) {
				foreach(Potion::getEffectsById($this->getPotionId()) as $effect) {
					$p->addEffect($effect);
				}
			}
		}
		parent::kill();
	}

	/**
	 * Override default projectile behaviour so potions don't knockback players
	 *
	 * @param Entity $with
	 * @param int $damage
	 */
	public function onEntityCollide(Entity $with, int $damage) {

	}

	public function spawnTo(Player $player) {
		$pk = new AddEntityPacket();
		$pk->type = ThrownPotion::NETWORK_ID;
		$pk->eid = $this->getId();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);
		parent::spawnTo($player);
	}

}