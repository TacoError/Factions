<?php namespace Taco\Factions\crates\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class GiveKeyCommand extends Command {

    public function __construct() {
        parent::__construct("givekey", "Give a key to a player.");
        $this->setPermission("core.crates");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (count($args) < 2) {
            $sender->sendMessage(Format::PREFIX_CRATE . "cUsage: /givekey (player) (crate) (amount = 1)");
            return;
        }
        if (is_null($player = Server::getInstance()->getPlayerByPrefix($args[0]))) {
            $sender->sendMessage(Format::PREFIX_CRATE . "cInvalid player.");
            return;
        }
        if (is_null($crate = Manager::getCrateManager()->getCrateFromName($args[1]))) {
            $sender->sendMessage(Format::PREFIX_CRATE . "cInvalid crate.");
            return;
        }
        $player->getInventory()->addItem($crate->makeKey()->setCount($args[2] ?? 1));
        $sender->sendMessage(Format::PREFIX_CRATE . "aGave key to player.");
        $player->sendMessage(Format::PREFIX_CRATE . "aYou have received a " . $crate->getFancyName() . "§r§a key!");
    }

}