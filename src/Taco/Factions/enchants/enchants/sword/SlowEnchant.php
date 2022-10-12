<?php namespace Taco\Factions\enchants\enchants\sword;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;
use Taco\Factions\enchants\types\SwordHitEnchant;

class SlowEnchant extends SwordHitEnchant {

    public function onHit(Player $killer, Player $hit, int $level) : void {
        $rand = mt_rand($level, max(10, $level) + 1);
        if ($rand > $level) return;
        $hit->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), (2 * 20), 2));
        $killer->sendMessage("*SLOW*");
        $hit->sendMessage("*YOU HAVE BEEN SLOWED*");
    }

    public function getFor() : string {
        return "Sword";
    }

    public function getDescription() : string {
        return "Slow your enemies.";
    }

}