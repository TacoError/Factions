<?php namespace Taco\Factions\sessions;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use Taco\Factions\Main;

class SessionManager {

    /** @var array<string, PlayerSession> */
    private array $sessions = [];

    /** @var Config */
    private Config $store;

    public function __construct() {
        $this->store = new Config(Main::getInstance()->getDataFolder() . "players.yml", Config::YAML);
        Server::getInstance()->getPluginManager()->registerEvents(new SessionListener(), Main::getInstance());
    }

    /**
     * Returns a players session
     *
     * @param Player $player
     * @return PlayerSession
     */
    public function getSession(Player $player) : PlayerSession {
        return $this->sessions[$player->getName()];
    }

    /**
     * Closes the players session
     *
     * @param Player $player
     * @return void
     */
    public function closeSession(Player $player) : void {
        $this->sessions[$player->getName()]->save();
        unset($this->sessions[$player->getName()]);
    }

    /**
     * Opens a session for the player
     *
     * @param Player $player
     * @return void
     */
    public function openSession(Player $player) : void {
        $this->sessions[$player->getName()] = new PlayerSession($player, $this->store);
    }

}