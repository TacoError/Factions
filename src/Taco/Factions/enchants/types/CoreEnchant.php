<?php namespace Taco\Factions\enchants\types;

use pocketmine\item\enchantment\Enchantment;

abstract class CoreEnchant extends Enchantment {

    abstract function getDescription() : string;

    abstract function getFor() : string;

}