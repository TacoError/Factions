<?php namespace Taco\Factions\factions\objects;

use pocketmine\player\Player;
use pocketmine\Server;

class FactionMember {

    /** @var string */
    private string $name;

    /** @var FactionRole */
    private FactionRole $role;

    public function __construct(string $name, FactionRole $role) {
        $this->name = $name;
        $this->role = $role;
    }

    /**
     * @param FactionRole $role
     * @return void
     */
    public function setRole(FactionRole $role) : void {
        $this->role = $role;
    }

    /*** @return FactionRole */
    public function getRole() : FactionRole {
        return $this->role;
    }

    /*** @return string */
    public function getName() : string {
        return $this->name;
    }

    /*** @return Player|null */
    public function getPlayer() : ?Player {
        return Server::getInstance()->getPlayerExact($this->name);
    }

}