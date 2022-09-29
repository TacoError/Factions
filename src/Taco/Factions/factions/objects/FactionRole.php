<?php namespace Taco\Factions\factions\objects;

class FactionRole {

    /** @var string */
    private string $name;

    /** @var array */
    private array $permissions;

    public function __construct(string $name, array $permissions) {
        $this->name = $name;
        $this->permissions = $permissions;
    }

    /*** @return string */
    public function getName() : string {
        return $this->name;
    }

    /**
     * @param int $permission
     * @return bool
     */
    public function hasPermission(int $permission) : bool {
        return in_array($permission, $this->permissions);
    }

    /*** @return array */
    public function getPermissions() : array {
        return $this->permissions;
    }

}