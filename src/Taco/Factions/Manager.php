<?php namespace Taco\Factions;

use Taco\Factions\factions\FactionManager;

class Manager {

    /** @var FactionManager */
    private static FactionManager $factionManager;

    public function __construct() {
        self::$factionManager = new FactionManager();
    }

    /*** @return FactionManager */
    public static function getFactionManager() : FactionManager {
        return self::$factionManager;
    }

}