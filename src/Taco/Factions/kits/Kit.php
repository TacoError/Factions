<?php namespace Taco\Factions\kits;

use pocketmine\item\Item;
use pocketmine\player\Player;
use Taco\Factions\Manager;
use Taco\Factions\sessions\PlayerSession;
use Taco\Factions\utils\Format;
use Taco\Factions\utils\PlayerUtils;

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

    /**
     * Gives the kit to the player
     *
     * @param Player $player
     * @return void
     */
    public function giveKit(Player $player) : void {
        Manager::getSessionManager()->getSession($player)->setKitCoolDown($this->name);
        foreach ($this->items as $item) {
            PlayerUtils::giveSafe($player, $item);
        }
        $player->sendMessage(Format::PREFIX_KITS . "eYou have equipped the kit \"" . $this->name . "\".");
    }

    /*** @return int */
    public function getCoolDown() : int {
        return $this->coolDown;
    }

    /*** @return string */
    public function getName() : string {
        return $this->name;
    }

    /**
     * Returns whether the specified player can equip the kit
     *
     * @param Player $player
     * @return bool
     */
    public function hasPermission(Player $player) : bool {
        if ($this->permission == "") return true;
        return $player->hasPermission($this->permission);
    }

    /**
     * Returns whether the player is on coolDown
     *
     * @param Player $player
     * @return bool
     */
    public function isOnCoolDown(Player $player) : bool {
        $coolDown = Manager::getSessionManager()->getSession($player)->getKitCoolDown($this->name);
        if ($coolDown < 1) return false;
        return (time() - $coolDown) < $coolDown;
    }

}