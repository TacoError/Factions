<?php namespace Taco\Factions\crates\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class KeyAllCommand extends Command {

    public function __construct() {
        parent::__construct("keyall", "Give key(s) to everyone online.");
        $this->setPermission("core.crates");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (count($args) < 1) {
            $sender->sendMessage(Format::PREFIX_CRATE . "cUsage: /keyall (crate) (amount = 1)");
            return;
        }
        if (is_null($crate = Manager::getCrateManager()->getCrateFromName($args[0]))) {
            $sender->sendMessage(Format::PREFIX_CRATE . "cInvalid crate.");
            return;
        }
        $sender->sendMessage(Format::PREFIX_CRATE . "aGave key to everyone online.");
        foreach(Server::getInstance()->getOnlinePlayers() as $player) {
            $player->getInventory()->addItem($crate->makeKey()->setCount($args[1] ?? 1));
            $player->sendMessage(Format::PREFIX_CRATE . "aYou have received a " . $crate->getFancyName() . "§r§a key!");
        }
    }

}