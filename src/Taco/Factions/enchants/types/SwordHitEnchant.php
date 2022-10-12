<?php namespace Taco\Factions\enchants\types;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\player\Player;

abstract class SwordHitEnchant extends CoreEnchant {

    abstract function onHit(Player $killer, Player $hit, int $level) : void;

}