# FastTransfer

Transfer vanilla Minecraft: PE clients to another server.
Works with Minecraft: PE 0.11.0 alpha build 6 or higher.

     FastTransfer plugin for PocketMine-MP
     Copyright (C) 2015 Shoghi Cervantes <https://github.com/shoghicp/FastTransfer>

     This program is free software: you can redistribute it and/or modify
     it under the terms of the GNU Lesser General Public License as published by
     the Free Software Foundation, either version 3 of the License, or
     (at your option) any later version.

     This program is distributed in the hope that it will be useful,
     but WITHOUT ANY WARRANTY; without even the implied warranty of
     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     GNU General Public License for more details.


## Commands

* `/transfer [player] <address> <port>` (defaults to OPs)


## Permissions

| Permission | Default | Description |
| :---: | :---: | :--- |
| fasttransfer.command.transfer | op | Allows to transfer players to another server |

## For developers

### Events

* shoghicp\FastTransfer\PlayerTransferEvent

### Plugin API methods

All methods are available through the main plugin object (plugin name is `FastTransfer`)

* bool transferPlayer(pocketmine\Player $player, $address, $port, $message)
* void cleanLookupCache()

Read the source code for more in-depth explanation
