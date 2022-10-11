<?php namespace Taco\Factions;

use Taco\Factions\crates\CratesManager;
use Taco\Factions\factions\FactionManager;
use Taco\Factions\groups\GroupManager;
use Taco\Factions\kits\KitManager;
use Taco\Factions\sessions\SessionManager;

class Manager {

    /** @var FactionManager */
    private static FactionManager $factionManager;

    /** @var GroupManager */
    private static GroupManager $groupManager;

    /** @var SessionManager */
    private static SessionManager $sessionManager;

    /** @var KitManager */
    private static KitManager $kitManager;

    /** @var CratesManager */
    private static CratesManager $crateManager;

    public function __construct(array $config) {
        self::$groupManager = new GroupManager($config["groups"]);
        self::$sessionManager = new SessionManager();
        self::$kitManager = new KitManager();
        self::$factionManager = new FactionManager();
        self::$factionManager->prepare();
        self::$crateManager = new CratesManager();
    }

    /*** @return FactionManager */
    public static function getFactionManager() : FactionManager {
        return self::$factionManager;
    }

    /*** @return GroupManager */
    public static function getGroupManager() : GroupManager {
        return self::$groupManager;
    }

    /*** @return SessionManager */
    public static function getSessionManager() : SessionManager {
        return self::$sessionManager;
    }

    /*** @return KitManager -*/
    public static function getKitManager() : KitManager {
        return self::$kitManager;
    }

    /*** @return CratesManager */
    public static function getCrateManager() : CratesManager {
        return self::$crateManager;
    }

}