<?php namespace Taco\Factions\sessions\economy;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class GiveMoneyCommand extends Command {

    public function __construct() {
        parent::__construct("givemoney", "Give money to a player (amount)");
        $this->setPermission("core.money");
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (count($args) < 2) {
            $sender->sendMessage("Incorrect usage, correct usage: /givemoney (player) (amount)");
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
        Manager::getSessionManager()->getSession($player)->giveMoney($amount);
        $sender->sendMessage("Gave $" . Format::intToPrefix($amount) . " to " . $player->getName());
    }

}