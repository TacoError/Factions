<?php namespace Taco\Factions\utils;

use pocketmine\item\Item;
use pocketmine\player\Player;

class PlayerUtils {

    public const PHP_MAX = 2147483647;

    /**
     * If the player can hold the item, it will add it
     * to their inventory, otherwise drop it at their position
     *
     * @param Player $player
     * @param Item $item
     * @return void
     */
    public static function giveSafe(Player $player, Item $item) : void {
        if ($player->getInventory()->canAddItem($item)) {
            $player->getInventory()->addItem($item);
            return;
        }
        $player->getWorld()->dropItem($player->getPosition()->asVector3(), $item);
    }

}