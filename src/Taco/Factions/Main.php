<?php namespace Taco\Factions;

use JsonException;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase {

    use SingletonTrait;

    public function onLoad() : void {
        self::setInstance($this);
    }

    public function onEnable() : void {
        new Manager();
    }

    /*** @throws JsonException */
    public function onDisable() : void {
        Manager::getFactionManager()->save();
    }

}