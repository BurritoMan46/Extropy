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

namespace pocketmine\plugin;

use pocketmine\event\plugin\PluginDisableEvent;
use pocketmine\event\plugin\PluginEnableEvent;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class FolderPluginLoader implements PluginLoader {

	/** @var Server */
	private $server;

	/**
	 * @param Server $server
	 */
	public function __construct(Server $server) {
		$this->server = $server;
	}

	/**
	 * Loads the plugin contained in $file
	 *
	 * @param string $file
	 *
	 * @return Plugin|null
	 */
	public function loadPlugin($file){
		if(is_dir($file) and file_exists($file . "/plugin.yml") and file_exists($file . "/src/")) {
			if(($description = $this->getPluginDescription($file)) instanceof PluginDescription) {
				$this->server->getLogger()->info(TextFormat::LIGHT_PURPLE . "Loading source plugin " . $description->getFullName());
				$dataFolder = dirname($file) . DIRECTORY_SEPARATOR . $description->getName();
				if(file_exists($dataFolder) and !is_dir($dataFolder)) {
					$this->server->getLogger()->warning("Projected dataFolder '" . $dataFolder . "' for source plugin " . $description->getName() . " exists and is not a directory");

					return null;
				}

				$className = $description->getMain();
				$this->server->getLoader()->addPath($file . "/src");

				if(class_exists($className, true)) {
					$plugin = new $className();
					$this->initPlugin($plugin, $description, $dataFolder, $file);

					return $plugin;
				} else {
					$this->server->getLogger()->warning("Couldn't load source plugin " . $description->getName() . ": main class not found");

					return null;
				}
			}
		}

		return null;
	}

	/**
	 * Gets the PluginDescription from the file
	 *
	 * @param string $file
	 *
	 * @return null|PluginDescription
	 */
	public function getPluginDescription($file) {
		if(is_dir($file) and file_exists($file . "/plugin.yml")) {
			$yaml = @file_get_contents($file . "/plugin.yml");
			if($yaml != "") {
				return new PluginDescription($yaml);
			}
		}

		return null;
	}

	/**
	 * Returns the filename patterns that this loader accepts
	 *
	 * @return string
	 */
	public function getPluginFilters() : string {
		return "/[^\\.]/";
	}

	/**
	 * @param PluginBase        $plugin
	 * @param PluginDescription $description
	 * @param string            $dataFolder
	 * @param string            $file
	 */
	private function initPlugin(PluginBase $plugin, PluginDescription $description, $dataFolder, $file) {
		$plugin->init($this, $this->server, $description, $dataFolder, $file);
		$plugin->onLoad();
	}

	/**
	 * @param Plugin $plugin
	 */
	public function enablePlugin(Plugin $plugin) {
		if($plugin instanceof PluginBase and !$plugin->isEnabled()) {
			$this->server->getLogger()->info("Enabling " . $plugin->getDescription()->getFullName());

			$plugin->setEnabled(true);

			$this->server->getPluginManager()->callEvent(new PluginEnableEvent($plugin));
		}
	}

	/**
	 * @param Plugin $plugin
	 */
	public function disablePlugin(Plugin $plugin) {
		if($plugin instanceof PluginBase and $plugin->isEnabled()) {
			$this->server->getLogger()->info("Disabling " . $plugin->getDescription()->getFullName());

			$this->server->getPluginManager()->callEvent(new PluginDisableEvent($plugin));

			$plugin->setEnabled(false);
		}
	}

}