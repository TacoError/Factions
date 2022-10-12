<?php namespace Taco\Factions\enchants\enchants\armor\boots;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;
use Taco\Factions\enchants\types\ArmorToggleEnchant;
use Taco\Factions\utils\PlayerUtils;

class SpringsEnchant extends ArmorToggleEnchant {

    public function getDescription() : string {
        return "Get a jump effect!";
    }

    public function onEquip(Player $player, int $level) : void {
        $player->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), PlayerUtils::PHP_MAX, $level - 1));
    }

    public function onUnEquip(Player $player) : void {
        $player->getEffects()->remove(VanillaEffects::JUMP_BOOST());
    }

    public function getFor() : string {
        return "Boots";
    }
}