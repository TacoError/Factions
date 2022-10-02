<?php namespace Taco\Factions\sessions\economy;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class PayCommand extends Command {

    public function __construct() {
        parent::__construct("pay", "Give money to another player.");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) return;
        if (count($args) < 2) {
            $sender->sendMessage(Format::PREFIX_ECO . "cIncorrect usage. Correct usage: /pay (player) (amount)");
            return;
        }
        if (is_null($player = Server::getInstance()->getPlayerByPrefix($args[0]))) {
            $sender->sendMessage(Format::PREFIX_ECO . "cThat player is not online or doesn't exist.");
            return;
        }
        $amount = $args[1];
        if (!is_numeric($amount) || $amount < 1) {
            $sender->sendMessage(Format::PREFIX_ECO . "cThe amount must be greater than 1.");
            return;
        }
        $session = Manager::getSessionManager()->getSession($sender);
        if ($session->getBalance() < $amount) {
            $sender->sendMessage(Format::PREFIX_ECO . "cYou do not have enough money to perform this transaction.");
            return;
        }
        $session->takeMoney($amount);
        Manager::getSessionManager()->getSession($player)->giveMoney($amount);
        $sender->sendMessage(Format::PREFIX_ECO . "aYou have paid $" . Format::intToPrefix($amount) . " to " . $player->getName() . ".");
        $player->sendMessage(Format::PREFIX_ECO . "aYou have received $" . Format::intToPrefix($amount) . " from " . $sender->getName() . ".");
    }

}