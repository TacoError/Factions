<?php namespace Taco\Factions\groups\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Taco\Factions\Manager;

class RemoveGroupCommand extends Command {

    public function __construct() {
        parent::__construct("removegroup", "Remove a group from a player.");
        $this->setAliases(["remgroup"]);
        $this->setPermission("core.groups");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (count($args) < 2) {
            $sender->sendMessage("Usage: /removegroup (player) (group)");
            return;
        }
        if (is_null($player = Server::getInstance()->getPlayerByPrefix($args[0]))) {
            $sender->sendMessage("That player is not online, or doesn't exist.");
            return;
        }
        if (is_null($group = Manager::getGroupManager()->getGroupFromName($args[1]))) {
            $sender->sendMessage("There is no group with that name.");
            return;
        }
        $session = Manager::getSessionManager()->getSession($player);
        if (!$session->isInGroup($group->getName())) {
            $sender->sendMessage("That player is not in the provided group.");
            return;
        }
        $session->removeGroup($group);
        $sender->sendMessage("The group \"" . $group->getName() . "\" has been taken from the player \"" . $player->getName() . "\"");
    }

}