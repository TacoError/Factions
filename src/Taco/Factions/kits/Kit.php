<?php namespace Taco\Factions\kits;

use pocketmine\item\Item;

class Kit {

    /** @var string */
    private string $name;

    /** @var string */
    private string $permission;

    /** @var array<Item> */
    private array $items;

    /** @var int */
    private int $coolDown;

    public function __construct(string $name, int $coolDown, array $items, string $permission = "") {
        $this->name = $name;
        $this->coolDown = $coolDown;
        $this->items = $items;
        $this->permission = $permission;
    }

}