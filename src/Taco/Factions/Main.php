<?php namespace Taco\Factions;

use JsonException;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase {

    use SingletonTrait;

    /** @var array<string, mixed> */
    public static array $config;

    public function onLoad() : void {
        self::setInstance($this);
    }

    public function onEnable() : void {
        $this->saveDefaultConfig();
        self::$config = $this->getConfig()->getAll();

        new Manager(self::$config);
    }

    /*** @throws JsonException */
    public function onDisable() : void {
        Manager::getFactionManager()->save();
    }

}