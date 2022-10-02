<?php namespace Taco\Factions\sessions\economy;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class BalanceCommand extends Command {

    public function __construct() {
        parent::__construct("balance", "Check your current balance. [optional: name]");
        $this->setAliases(["bal", "mymoney"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) return;
        if (count($args) > 0) {
            if (is_null($player = Server::getInstance()->getPlayerByPrefix($args[0]))) {
                $sender->sendMessage(Format::PREFIX_ECO . "cThat player is not online, or doesn't exist.");
                return;
            }
            $balance = Format::intToPrefix(Manager::getSessionManager()->getSession($player)->getBalance());
            $sender->sendMessage(Format::PREFIX_ECO . "a" . $player->getName() . "'s balance: $" . $balance);
            return;
        }
        $balance = Format::intToPrefix(Manager::getSessionManager()->getSession($sender)->getBalance());
        $sender->sendMessage(Format::PREFIX_ECO . "aBalance: $" . $balance);
    }

}