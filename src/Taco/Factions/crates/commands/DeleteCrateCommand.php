<?php namespace Taco\Factions\crates\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class DeleteCrateCommand extends Command {

    public function __construct() {
        parent::__construct("delcrate", "Delete a crate.");
        $this->setPermission("core.crates");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (count($args) < 1) {
            $sender->sendMessage(Format::PREFIX_CRATE . "cUsage: /delcrate (name)");
            return;
        }
        if (is_null(Manager::getCrateManager()->getCrateFromName($args[0]))) {
            $sender->sendMessage(Format::PREFIX_CRATE . "cInvalid crate.");
            return;
        }
        Manager::getCrateManager()->deleteCrate($args[0]);
        $sender->sendMessage(Format::PREFIX_CRATE . "aCrate deleted.");
    }

}