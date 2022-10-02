<?php namespace Taco\Factions\kits\commands;

use JsonException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Taco\Factions\Manager;

class DeleteKitCommand extends Command {

    public function __construct() {
        parent::__construct("deletekit", "Delete a kit. /editkit (name)");
        $this->setPermission("core.kits");
    }

    /*** @throws JsonException */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender) || !$sender instanceof Player) return;
        if (count($args) < 1) {
            $sender->sendMessage("Incorrect usage, correct usage: /deletekit (name)");
            return;
        }
        $name = $args[0];
        $km = Manager::getKitManager();
        if (is_null($kit = $km->getKitFromName($name))) {
            $sender->sendMessage("There is no kit with that name.");
            return;
        }
        Manager::getKitManager()->deleteKit($name);
        $sender->sendMessage("Kit deleted.");
    }

}