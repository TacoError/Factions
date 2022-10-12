<?php namespace Taco\Factions\enchants\types;

use pocketmine\block\Block;
use pocketmine\player\Player;

abstract class PickaxeEnchant extends CoreEnchant {

    abstract function blockBreak(Player $player, Block $block, int $level) : void;

}