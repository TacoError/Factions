<?php namespace Taco\Factions\crates;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\world\Position;
use WolfDen133\WFT\Texts\FloatingText;
use WolfDen133\WFT\WFT;

class Crate {

    /** @var string */
    private string $name;

    /** @var string */
    private string $fancyName;

    /** @var array<string, array<string, int|string>> */
    private array $rewards;

    /** @var FloatingText */
    private FloatingText $text;

    /** @var Position */
    private Position $position;

    public function __construct(string $name, string $fancyName, array $rewards, Position $pos) {
        $this->name = $name;
        $this->fancyName = $fancyName;
        $this->rewards = $rewards;
        $this->position = $pos;

        $text = new FloatingText(new Position(
            $pos->getX() + 0.5,
            $pos->getY() + 1.5,
            $pos->getZ() + 0.5,
            $pos->getWorld()
        ),
            $this->name,
            $this->fancyName . "\n§r§7Tap me with a key to open..."
        );
        WFT::getAPI()->registerText($text);
        WFT::getAPI()::spawnToAll($text);
    }

    /*** @return string */
    public function getName() : string {
        return $this->name;
    }

    /**
     * Opens the drops menu
     *
     * @param Player $player
     * @return void
     */
    public function openDropsMenu(Player $player) : void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->send($player, $this->fancyName . "§r Crate");
        $menu->setListener(function(InvMenuTransaction $transaction) : InvMenuTransactionResult {
            return $transaction->discard();
        });
        $inv = $menu->getInventory();
        $curr = 0;
        foreach ($this->rewards as $data) {
            $exploded = explode(":", $data["item"]);
            $item = ItemFactory::getInstance()->get(
                (int)$exploded[0],
                (int)$exploded[1]
            );
            $inv->setItem($curr, $item->setCustomName($data["custom-name"] ?? $item->getName()));
            $curr++;
        }
    }

    /**
     * Returns a random command to be executed.
     *
     * @return string
     */
    public function getRandomReward() : string {
        foreach ($this->rewards as $reward) {
            if (mt_rand(1, 2) < 2) {
                return $reward["reward"];
            }
        }
        return $this->rewards[0]["reward"];
    }

    /**
     * Return a key for the crate
     *
     * @return Item
     */
    public function getKey() : Item {
        $key = VanillaItems::NETHER_STAR();
        $key->setCustomName($this->fancyName . "§r§f Crate key");
        $key->setLore([
            "§r§7Click this key on a crate",
            "§r§7to get a random reward."
        ]);
        $key->getNamedTag()->setString("crateKey", $this->name);
        return $key;
    }

    /**
     * Returns a random key
     *
     * @param Item $item
     * @return bool
     */
    public function isValidKey(Item $item) : bool {
        if (is_null($type = $item->getNamedTag()->getTag("crateKey"))) return false;
        if ($type->getValue() == $this->name) return true;
        return false;
    }

    /*** @return Position */
    public function getPosition() : Position {
        return $this->position;
    }

}