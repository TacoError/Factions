<?php namespace Taco\Factions\sessions;

use pocketmine\player\Player;
use pocketmine\utils\Config;
use Taco\Factions\groups\Group;
use Taco\Factions\Manager;

class PlayerSession {

    /** @var Player */
    private Player $player;

    /** @var Config */
    private Config $store;

    /** @var int */
    private int $kills;

    /** @var int */
    private int $deaths;

    /** @var int */
    private int $killStreak;

    /** @var int */
    private int $bestKillStreak;

    /** @var array<string> */
    private array $permissions;

    /** @var array */
    private array $groups;

    public function __construct(Player $player, Config $store) {
        $this->player = $player;
        $this->store = $store;

        $gm = Manager::getGroupManager();
        if ($store->exists($player->getName())) {
            $data = $store->get($player->getName());
            $this->kills = $data["kills"] ?? 0;
            $this->deaths = $data["deaths"] ?? 0;
            $this->killStreak = $data["killStreak"] ?? 0;
            $this->bestKillStreak = $data["bestKillStreak"] ?? 0;
            $groups = $data["groups"] ?? [$gm->getDefaultGroup()->getName()];
            $this->groups = array_map(fn($iGroup) => $gm->getGroupFromName($iGroup), $groups);
            $this->permissions = $data["permissions"] ?? [];
            return;
        }

    }

    /*** @return void */
    public function save() : void {

    }

    /**
     * @param string $permission
     * @return void
     */
    public function removePermission(string $permission) : void {
        $this->permissions = array_diff($this->permissions, [$permission]);
    }

    /**
     * @param string $permission
     * @return void
     */
    public function addPermission(string $permission) : void {
        $this->permissions[] = $permission;
    }

    /**
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission) : bool {
        return in_array($permission, $this->permissions);
    }

    /**
     * Adds a group to the players list of groups
     *
     * @param Group $group
     * @return void
     */
    public function addGroup(Group $group) : void {
        $this->groups[] = $group;
    }

    /**
     * Removes one of the players groups
     *
     * @param Group $group
     * @return void
     */
    public function removeGroup(Group $group) : void {
        $this->groups = array_filter($this->groups, fn($iGroup) => $iGroup->getName() !== $group->getName());
    }

    /*** @return array<Group> */
    public function getGroups() : array {
        return $this->groups;
    }

    /**
     * Returns whether the player is in said group
     *
     * @param string $group
     * @return bool
     */
    public function isInGroup(string $group) : bool {
        return in_array(
            $group,
            array_map(fn($iGroup) => $iGroup->getName(), $this->groups)
        );
    }

}