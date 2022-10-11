<?php namespace Taco\Factions\crates\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class KeyAllCommand extends Command {

    public function __construct() {
        parent::__construct("keyall", "Give key(s) to everyone on the server.");
        $this->setPermission("core.crates");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (count($args) < 1) {
            $sender->sendMessage("Correct usage: /keyall (key) (amount = 1)");
            return;
        }
        if (is_null($crate = Manager::getCrateManager()->getCrateFromName($args[1]))) {
            $sender->sendMessage("Please provide a valid crate.");
            return;
        }
        $sender->sendMessage("Gave key to players");
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $player->sendMessage(Format::PREFIX_CRATE . "eYou have received a key.");
            $player->getInventory()->addItem($crate->getKey()->setCount($args[2] ?? 1));
        }
    }

}