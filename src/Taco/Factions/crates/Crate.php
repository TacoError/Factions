<?php namespace Taco\Factions\crates;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use Taco\Factions\utils\Format;
use WolfDen133\WFT\Texts\FloatingText;

class Crate {

    /** @var string */
    private string $name;

    /** @var string */
    private string $fancyName;

    /** @var array<Item> */
    private array $rewards = [];

    public function __construct(string $name, string $fancyName, array $rewards) {
        $this->name = $name;
        $this->fancyName = $fancyName;
        $this->rewards = $rewards;
    }

    /*** @return string */
    public function getName() : string {
        return $this->name;
    }

    /*** @return string */
    public function getFancyName() : string {
        return $this->fancyName;
    }

    /*** @return array */
    public function getJSONRewards() : array {
        return array_map(fn($item) => $item->jsonSerialize(), $this->rewards);
    }

    /**
     * Gives random reward from this crate to the player
     *
     * @param Player $player
     * @return void
     */
    public function giveRandomRewardTo(Player $player) : void {
        $reward = $this->rewards[array_rand($this->rewards)];
        if (!$player->getInventory()->canAddItem($reward)) {
            $player->getWorld()->dropItem($player->getPosition()->asVector3(), $reward);
            $player->sendMessage(Format::PREFIX_CRATE . "cYour inventory could not hold that item, so it has been dropped on the floor.");
            return;
        }
        $player->getInventory()->addItem($reward);
    }

    /*** @return Item */
    public function makeKey() : Item {
        $key = VanillaItems::NETHER_STAR();
        $key->setCustomName($this->fancyName . "§r§f Key");
        $key->setLore([
            "§r§7Tap the \"" . $this->fancyName . "§r§7\" crate with",
            "§r§7this key to open the crate!"
        ]);
        $key->getNamedTag()->setString("crateType", $this->name);
        return $key;
    }

    /**
     * Returns whether the specified item is for
     * this crate.
     *
     * @param Item $item
     * @return bool
     */
    public function isKeyForCrate(Item $item) : bool {
        if (is_null($type = $item->getNamedTag()->getTag("crateType"))) {
            return false;
        }
        return $type->getValue() == $this->name;
    }

    /**
     * Opens the menu showing the possible rewards.
     *
     * @param Player $player
     * @return void
     */
    public function openRewardsMenu(Player $player) : void {
        $menu = InvMenu::create(count($this->rewards) > 26 ? InvMenuTypeIds::TYPE_DOUBLE_CHEST : InvMenuTypeIds::TYPE_CHEST);
        $menu->setListener(InvMenu::readonly());
        $menu->send($player, "Possible rewards for \"" . $this->name . "\"");
        $menu->getInventory()->setContents($this->rewards);
    }

    /**
     * Opens the chest the player will edit the rewards in.
     *
     * @param Player $player
     * @return void
     */
    public function openEditRewardsChest(Player $player) : void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->send($player, "Edit Rewards for \"" . $this->name . "\"");
        $inventory = $menu->getInventory();
        $inventory->setContents($this->rewards);
        $menu->setInventoryCloseListener(function(Player $player, Inventory $inventory) : void {
            $this->rewards = $inventory->getContents();
            $player->sendMessage(Format::PREFIX_CRATE . "eChanged items for \"" . $this->name . "\"");
        });
    }

}