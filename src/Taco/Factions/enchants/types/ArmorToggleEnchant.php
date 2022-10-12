<?php namespace Taco\Factions\enchants\types;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\player\Player;

abstract class ArmorToggleEnchant extends CoreEnchant {

    abstract function onEquip(Player $player, int $level) : void;

    abstract function onUnEquip(Player $player) : void;

}