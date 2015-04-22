<?php

/*
 * FastTransfer plugin for PocketMine-MP
 * Copyright (C) 2015 Shoghi Cervantes <https://github.com/shoghicp/FastTransfer>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

namespace shoghicp\FastTransfer;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\network\Network;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class FastTransfer extends PluginBase{

	private $lookup = [];

	/**
	 * Will transfer a connected player to another server.
	 * This will trigger PlayerTransferEvent
	 *
	 * Player transfer might not be instant if you use a DNS address instead of an IP address
	 *
	 * @param Player $player
	 * @param string $address
	 * @param int    $port
	 * @param string $message If null, ignore message
	 *
	 * @return bool
	 */
	public function transferPlayer(Player $player, $address, $port = 19132, $message = "You are being transferred"){
		$ev = new PlayerTransferEvent($player, $address, $port, $message);
		$this->getServer()->getPluginManager()->callEvent($ev);
		if($ev->isCancelled()){
			return false;
		}

		$ip = $this->lookupAddress($ev->getAddress());

		if($ip === null){
			return false;
		}
		
		if($message !== null and $message !== ""){
			$player->sendMessage($message);	
		}

		$packet = new StrangePacket();
		$packet->address = $ip;
		$packet->port = $ev->getPort();
		$player->dataPacket($packet->setChannel(Network::CHANNEL_ENTITY_SPAWNING));

		return true;
	}

	/**
	 * Clear the DNS lookup cache.
	 */
	public function cleanLookupCache(){
		$this->lookup = [];
	}


	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		if($label === "transfer"){
			if(count($args) < 2 or count($args) > 3 or (count($args) === 2 and !($sender instanceof Player))){
				$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$command->getUsage()]));

				return true;
			}

			/** @var Player $target */
			$target = $sender;

			if(count($args) === 3){
				$target = $sender->getServer()->getPlayer($args[0]);
				$address = $args[1];
				$port = (int) $args[2];
			}else{
				$address = $args[0];
				$port = (int) $args[1];
			}

			if($target === null){
				$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.player.notFound"));
				return true;
			}

			$sender->sendMessage("Transferring player " . $target->getDisplayName() . " to $address:$port");
			if(!$this->transferPlayer($target, $address, $port)){
				$sender->sendMessage(TextFormat::RED . "An error occurred during the transfer");
			}

			return true;
		}

		return false;
	}

	/**
	 * @param $address
	 *
	 * @return null|string
	 */
	private function lookupAddress($address){
		//IP address
		if(preg_match("/^[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}$/", $address) > 0){
			return $address;
		}

		$address = strtolower($address);

		if(isset($this->lookup[$address])){
			return $this->lookup[$address];
		}

		$host = gethostbyname($address);
		if($host === $address){
			return null;
		}

		$this->lookup[$address] = $host;
		return $host;
	}
}
