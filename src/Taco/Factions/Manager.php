<?php namespace Taco\Factions;

use Taco\Factions\factions\FactionManager;
use Taco\Factions\groups\GroupManager;
use Taco\Factions\sessions\SessionManager;

class Manager {

    /** @var FactionManager */
    private static FactionManager $factionManager;

    /** @var GroupManager */
    private static GroupManager $groupManager;

    /** @var SessionManager */
    private static SessionManager $sessionManager;

    public function __construct(array $config) {
        self::$groupManager = new GroupManager($config["groups"]);
        self::$sessionManager = new SessionManager();
        self::$factionManager = new FactionManager();
        self::$factionManager->prepare();
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

}