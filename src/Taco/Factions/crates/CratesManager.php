<?php namespace Taco\Factions\crates;

use JsonException;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use Taco\Factions\crates\commands\DeleteCrateCommand;
use Taco\Factions\crates\commands\EditCrateCommand;
use Taco\Factions\crates\commands\GiveKeyCommand;
use Taco\Factions\crates\commands\KeyAllCommand;
use Taco\Factions\crates\commands\MakeCrateCommand;
use Taco\Factions\crates\commands\SetCratePositionCommand;
use Taco\Factions\Main;
use Taco\Factions\utils\VectorUtils;
use WolfDen133\WFT\Texts\FloatingText;
use WolfDen133\WFT\WFT;

class CratesManager {

    /** @var array<Crate> */
    private array $crates = [];

    /** @var Config */
    private Config $cratesStore;

    /** @var array */
    private array $positions;

    /** @var array<string, string> */
    public array $settingPositions = [];

    /** @var array<FloatingText> */
    private array $texts = [];

    public function __construct() {
        $this->cratesStore = new Config(Main::getInstance()->getDataFolder() . "crates.yml", Config::YAML, [
            "crates" => [],
            "positions" => []
        ]);
        $this->positions = $this->cratesStore->get("positions");
        foreach ($this->cratesStore->get("crates") as $name => $data) {
            $this->crates[] = new Crate($name, $data["fancyName"], array_map(fn($item) => Item::jsonDeserialize($item), $data["rewards"]));
        }

        $server = Server::getInstance();
        $server->getPluginManager()->registerEvents(new CrateListener(), Main::getInstance());
        $server->getCommandMap()->registerAll("Factions", [
            new DeleteCrateCommand(),
            new EditCrateCommand(),
            new MakeCrateCommand(),
            new SetCratePositionCommand(),
            new GiveKeyCommand(),
            new KeyAllCommand()
        ]);
        $this->setTexts();
    }

    /**
     * Saves crates to file
     *
     * @return void
     * @throws JsonException
     */
    public function save() : void {
        $crates = [];
        foreach ($this->crates as $crate) {
            $crates[$crate->getName()] = [
                "fancyName" => $crate->getFancyName(),
                "rewards" => $crate->getJSONRewards()
            ];
        }

        $store = $this->cratesStore;
        $store->set("crates", $crates);
        $store->set("positions", $this->positions);
        $store->save();
    }

    /**
     * Makes a crate
     *
     * @param string $name
     * @param string $fancyName
     * @return void
     */
    public function makeCrate(string $name, string $fancyName) : void {
        $this->crates[] = new Crate($name, $fancyName, []);
    }

    /**
     * Opens the edit crate items menu for the player
     *
     * @param Player $player
     * @param string $name
     * @return void
     */
    public function editCrateItems(Player $player, string $name) : void {
        $this->crates[$name]->openEditRewardsChest($player);
    }

    /**
     * Returns the crate at the position
     *
     * @param Position $position
     * @return Crate|null
     */
    public function getCrateAt(Position $position) : ?Crate {
        if (!isset($this->positions[$position = VectorUtils::positionToString($position)])) return null;
        return $this->getCrateFromName($this->positions[$position]);
    }

    /**
     * @param string $name
     * @return Crate|null
     */
    public function getCrateFromName(string $name) : ?Crate {
        foreach ($this->crates as $crate) {
            if ($crate->getName() !== $name) continue;
            return $crate;
        }
        return null;
    }

    /**
     * Deletes a crate
     *
     * @param string $name
     * @return void
     */
    public function deleteCrate(string $name) : void {
        $list = [];
        foreach ($this->positions as $pos => $crate) {
            if ($crate == $name) continue;
            $list[$pos] = $crate;
        }
        $this->positions = $list;
    }

    /**
     * @param string $position
     * @return void
     */
    public function removePosition(string $position) : void {
        if (!isset($this->positions[$position])) return;
        unset($this->positions[$position]);
        $this->setTexts();
    }

    /**
     * @param string $position
     * @param string $crate
     * @return void
     */
    public function addPosition(string $position, string $crate) : void {
        $this->positions[$position] = $crate;
        $this->setTexts();
    }

    /*** @return void */
    public function setTexts() : void {
        foreach ($this->texts as $floatingText) {
            WFT::getAPI()::closeToAll($floatingText);
            WFT::getAPI()->removeText($floatingText);
        }
        foreach ($this->positions as $position => $type) {
            $crate = $this->getCrateFromName($type);
            $pos = VectorUtils::stringToPosition($position);
            $text = new FloatingText(
                new Position($pos->getX() + 0.5, $pos->getY() + 1.5, $pos->getZ() + 0.5, $pos->getWorld()),
                $type,
                $crate->getFancyName() . "§r§f Crate\n§r§7Tap me with a key to open!"
            );
            WFT::getAPI()->registerText($text, false);
            WFT::getAPI()::spawnToAll($text);
            $this->texts[] = $text;
        }
    }

}