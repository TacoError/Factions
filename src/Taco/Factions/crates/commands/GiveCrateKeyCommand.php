<?php namespace Taco\Factions\crates\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class GiveCrateKeyCommand extends Command {

    public function __construct() {
        parent::__construct("givekey", "Give a key to a player");
        $this->setPermission("core.crates");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (count($args) < 2) {
            $sender->sendMessage("Correct usage: /givekey (player) (key) (amount = 1)");
            return;
        }
        if (is_null($player = Server::getInstance()->getPlayerByPrefix($args[0]))) {
            $sender->sendMessage("That player is not online or doesn't exist.");
            return;
        }
        if (is_null($crate = Manager::getCrateManager()->getCrateFromName($args[1]))) {
            $sender->sendMessage("Please provide a valid crate.");
            return;
        }
        $sender->sendMessage("Gave key to player");
        $player->sendMessage(Format::PREFIX_CRATE . "eYou have received a key.");
        $player->getInventory()->addItem($crate->getKey()->setCount($args[2] ?? 1));
    }

}