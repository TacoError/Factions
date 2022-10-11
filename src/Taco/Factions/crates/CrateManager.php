<?php namespace Taco\Factions\crates;

use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use Taco\Factions\crates\commands\GiveCrateKeyCommand;
use Taco\Factions\crates\commands\KeyAllCommand;
use Taco\Factions\Main;

class CrateManager {

    /** @var array<Crate> */
    private array $crates = [];

    public function __construct() {
        $server = Server::getInstance();

        $crates = Main::$config["crates"];
        foreach ($crates as $name => $data) {
            $exp = explode(":", $data["pos"]);
            $this->crates[] = new Crate(
                $name,
                $data["fancyName"],
                $data["rewards"],
                new Position(
                    (int)$exp[0],
                    (int)$exp[1],
                    (int)$exp[2],
                    $server->getWorldManager()->getWorldByName($exp[3])
                )
            );
        }

        $server->getCommandMap()->registerAll("Factions", [
            new GiveCrateKeyCommand(),
            new KeyAllCommand()
        ]);
        $server->getPluginManager()->registerEvents(new CrateListener(), Main::getInstance());
    }

    /**
     * Returns a crate from its name
     *
     * @param string $name
     * @return Crate|null
     */
    public function getCrateFromName(string $name) : ?Crate {
        foreach ($this->crates as $crate) {
            if ($crate->getName() == $name) return $crate;
        }
        return null;
    }

    /**
     * Returns the crate at the position, otherwise null
     *
     * @param Position $pos
     * @return Crate|null
     */
    public function getCrateAt(Position $pos) : ?Crate {
        foreach ($this->crates as $crate) {
            $cp = $crate->getPosition();
            if (
                $cp->getFloorX() == $pos->getFloorX() &&
                $cp->getFloorY() == $pos->getFloorY() &&
                $cp->getFloorZ() == $pos->getFloorZ() &&
                $cp->getWorld()->getDisplayName() == $pos->getWorld()->getDisplayName()
            ) return $crate;
        }
        return null;
    }

}