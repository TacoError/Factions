<?php namespace Taco\Factions\sessions;

use JsonException;
use pocketmine\permission\PermissionAttachment;
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

    /** @var array<Group> */
    private array $groups;

    /** @var array<string, int> */
    private array $kitCoolDowns;

    /** @var array<PermissionAttachment> */
    private array $attatchments = [];

    public function __construct(Player $player, Config $store) {
        $this->player = $player;
        $this->store = $store;

        $gm = Manager::getGroupManager();
        $data = $store->exists($player->getName()) ? $store->get($player->getName()) : [];
        $this->kills = $data["kills"] ?? 0;
        $this->deaths = $data["deaths"] ?? 0;
        $this->killStreak = $data["killStreak"] ?? 0;
        $this->bestKillStreak = $data["bestKillStreak"] ?? 0;
        $groups = $data["groups"] ?? [$gm->getDefaultGroup()->getName()];
        $this->groups = array_map(fn($iGroup) => $gm->getGroupFromName($iGroup), $groups);
        $this->permissions = $data["permissions"] ?? [];
        $this->kitCoolDowns = $data["kitCoolDowns"] ?? [];

        $this->reloadPermissions();
    }

    /***
     * @return void
     * @throws JsonException
     */
    public function save() : void {
        $this->unloadPermissions();
        $store = $this->store;
        $store->set($this->player->getName(), [
            "kills" => $this->kills,
            "deaths" => $this->deaths,
            "killStreak" => $this->killStreak,
            "bestKillStreak" => $this->bestKillStreak,
            "groups" => array_map(fn($group) => $group->getName(), $this->groups),
            "permissions" => $this->permissions,
            "kitCoolDowns" => $this->kitCoolDowns
        ]);
        $store->save();
    }

    /**
     * Sets the kits coolDown as time()
     *
     * @param string $kit
     * @return void
     */
    public function setKitCoolDown(string $kit) : void {
        $this->kitCoolDowns[$kit] = time();
    }

    /**
     * Returns the kit coolDown otherwise 0
     *
     * @param string $kit
     * @return int
     */
    public function getKitCoolDown(string $kit) : int {
        return $this->kitCoolDowns[$kit] ?? 0;
    }

    /**
     * Returns all available permissions (groups and singular)
     *
     * @return array
     */
    public function getPermissions() : array {
        $permissions = [];
        foreach ($this->groups as $group) {
            foreach ($group->getPermissions() as $permission) {
                if (in_array($permission, $permissions)) continue;
                $permissions[] = $permission;
            }
        }
        foreach ($this->permissions as $permission) {
            if (in_array($permission, $permissions)) continue;
            $permissions[] = $permissions;
        }
        return $permissions;
    }

    /*** @return void */
    public function unloadPermissions() : void {
        foreach ($this->attatchments as $attachment) {
            $this->player->removeAttachment($attachment);
        }
    }

    /**
     * Reloads all attachments, singular permissions,
     * and group permissions
     *
     * @return void
     */
    public function reloadPermissions() : void {
        $this->unloadPermissions();
        $permissions = $this->getPermissions();
        foreach ($permissions as $permission) {
            $attachment = $this->player->addAttachment($permission);
            $this->attatchments[] = $attachment;
        }
    }

    /**
     * @param string $permission
     * @return void
     */
    public function removePermission(string $permission) : void {
        $this->permissions = array_diff($this->permissions, [$permission]);
        $this->reloadPermissions();
    }

    /**
     * @param string $permission
     * @return void
     */
    public function addPermission(string $permission) : void {
        $this->permissions[] = $permission;
        $this->reloadPermissions();
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
        $this->reloadPermissions();
    }

    /**
     * Removes one of the players groups
     *
     * @param Group $group
     * @return void
     */
    public function removeGroup(Group $group) : void {
        $this->groups = array_filter($this->groups, fn($iGroup) => $iGroup->getName() !== $group->getName());
        $this->reloadPermissions();
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