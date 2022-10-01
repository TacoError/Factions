<?php namespace Taco\Factions\sessions\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Taco\Factions\Manager;

class AddPermissionCommand extends Command {

    public function __construct() {
        parent::__construct("addpermission", "Add a permission to a player.");
        $this->setPermission("core.permissions");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (count($args) < 2) {
            $sender->sendMessage("Usage: /addpermission (player) (permission)");
            return;
        }
        if (is_null($player = Server::getInstance()->getPlayerByPrefix($args[0]))) {
            $sender->sendMessage("That player is not online, or doesn't exist.");
            return;
        }
        $session = Manager::getSessionManager()->getSession($player);
        if ($session->hasPermission($args[1])) {
            $sender->sendMessage("That player already has that permission.");
            return;
        }
        $session->addPermission($args[1]);
        $sender->sendMessage("The permission \"" . $args[1] . "\" has been added to the player \"" . $player->getName() . "\"");
    }

}