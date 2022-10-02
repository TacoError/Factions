<?php namespace Taco\Factions\kits;

use JsonException;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use Taco\Factions\kits\commands\CreateKitCommand;
use Taco\Factions\kits\commands\DeleteKitCommand;
use Taco\Factions\kits\commands\EditKitCommand;
use Taco\Factions\kits\commands\KitsCommand;
use Taco\Factions\Main;

class KitManager {

    /** @var array<string, Kit> */
    private array $kits;

    /** @var Config */
    private Config $store;

    public function __construct() {
        $this->store = new Config(Main::getInstance()->getDataFolder() . "kits.yml", Config::YAML);

        $this->reloadKits();

        Server::getInstance()->getCommandMap()->registerAll("Factions", [
            new KitsCommand(),
            new CreateKitCommand(),
            new EditKitCommand(),
            new DeleteKitCommand()
        ]);
    }

    /*** @return void */
    public function reloadKits() : void {
        $this->kits = [];
        foreach ($this->store->getAll() as $name => $data) {
            $items = [];
            foreach ($data["items"] as $json) {
                $items[] = Item::jsonDeserialize($json);
            }

            $this->kits[$name] = new Kit(
                $data["name"] ?? "Kit",
                $data["coolDown"] ?? 0,
                $items,
                $data["permission"] ?? ""
            );
        }
    }

    /**
     * Returns a array of kits
     * the player is allowed to use
     *
     * @param Player $player
     * @return array<Kit>
     */
    public function getAllowedKits(Player $player) : array {
        return array_values(array_filter($this->kits, fn($kit) => $kit->hasPermission($player)));
    }

    /**
     * Creates a kit
     *
     * @param string $name
     * @param int $coolDown
     * @param string $permission
     * @param array<Item> $items
     * @return void
     * @throws JsonException
     */
    public function createKit(string $name, int $coolDown, string $permission, array $items) : void {
        $nItems = [];
        foreach ($items as $item) {
            $nItems[] = $item->jsonSerialize();
        }
        $this->store->set($name, [
            "name" => $name,
            "coolDown" => $coolDown,
            "items" => $nItems,
            "permission" => $permission
        ]);
        $this->store->save();
        $this->reloadKits();
    }

    /**
     * Deletes a kit
     *
     * @param string $name
     * @return void
     * @throws JsonException
     */
    public function deleteKit(string $name) : void {
        $this->store->remove($name);
        $this->store->save();
        $this->reloadKits();
    }

    /**
     * Edits a kits items
     *
     * @param string $name
     * @param array<Item> $newItems
     * @return void
     * @throws JsonException
     */
    public function editKitItems(string $name, array $newItems) : void {
        $nItems = [];
        foreach ($newItems as $item) {
            $nItems[] = $item->jsonSerialize();
        }
        $data = $this->store->get($name);
        $this->store->set($name, [
            "name" => $name,
            "coolDown" => $data["coolDown"],
            "items" => $nItems,
            "permission" => $data["permission"]
        ]);
        $this->store->save();
        $this->reloadKits();
    }

    /**
     * Returns a kit from its name, otherwise null
     *
     * @param string $name
     * @return Kit|null
     */
    public function getKitFromName(string $name) : ?Kit {
        return $this->kits[$name] ?? null;
    }

}