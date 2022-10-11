<?php namespace Taco\Factions\groups;

class Group {

    /** @var string */
    private string $name;

    /** @var string */
    private string $fancyName;

    /** @var array<string> */
    private array $permissions;

    /** @var int */
    private int $authority;

    public function __construct(string $name, string $fancyName, array $permissions, int $authority = 0) {
        $this->name = $name;
        $this->permissions = $permissions;
        $this->authority = $authority;
        $this->fancyName = $fancyName;
    }

    /*** @return string */
    public function getName() : string {
        return $this->name;
    }

    /*** @return array|string[] */
    public function getPermissions() : array {
        return $this->permissions;
    }

    /*** @return int */
    public function getAuthority() : int {
        return $this->authority;
    }

    /*** @return string */
    public function getFancyName() : string {
        return $this->fancyName;
    }

}