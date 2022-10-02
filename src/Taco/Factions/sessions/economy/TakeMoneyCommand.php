<?php namespace Taco\Factions\sessions\economy;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class TakeMoneyCommand extends Command {

    public function __construct() {
        parent::__construct("takemoney", "Take money from a player (amount)");
        $this->setPermission("core.money");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (count($args) < 2) {
            $sender->sendMessage("Incorrect usage, correct usage: /takemoney (player) (amount)");
            return;
        }
        if (is_null($player = Server::getInstance()->getPlayerByPrefix($args[0]))) {
            $sender->sendMessage("That player is not online, or doesn't exist.");
            return;
        }
        $amount = $args[1];
        if (!is_numeric($amount)) {
            $sender->sendMessage("The amount must be a number.");
            return;
        }
        Manager::getSessionManager()->getSession($player)->takeMoney($amount);
        $sender->sendMessage("Removed $" . Format::intToPrefix($amount) . " from " . $player->getName() . "'s balance.");
    }

}