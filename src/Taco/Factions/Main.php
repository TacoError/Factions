<?php namespace Taco\Factions;

use JsonException;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase {

    use SingletonTrait;

    /** @var array<string, mixed> */
    public static array $config;

    /** @var bool */
    private bool $save = true;

    public function onLoad() : void {
        self::setInstance($this);
    }

    public function onEnable() : void {
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }

        $cfgVer = 0;
        $this->saveDefaultConfig();
        self::$config = $this->getConfig()->getAll();
        if (!isset(self::$config["cfg-ver"]) || self::$config["cfg-ver"] !== $cfgVer) {
            $this->save = false;
            $this->getLogger()->error("CONFIG OUTDATED. (your version: " . self::$config["cfg-ver"] . " new version: " . $cfgVer . ")");
            $this->getLogger()->notice("Check here on how to fix: https://github.com/TacoError/Factions#Configuration");
            $this->getServer()->shutdown();
            return;
        }

        new Manager(self::$config);
    }

    /*** @throws JsonException */
    public function onDisable() : void {
        if (!$this->save) return;
        Manager::getFactionManager()->save();
    }

}