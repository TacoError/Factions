<?php namespace Taco\Factions\crates\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class MakeCrateCommand extends Command {

    public function __construct() {
        parent::__construct("makecrate", "Make a crate.");
        $this->setPermission("core.crates");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (count($args) < 2) {
            $sender->sendMessage(Format::PREFIX_CRATE . "cUsage: /makecrate (name) (fancyName)");
            return;
        }
        Manager::getCrateManager()->makeCrate($args[0], $args[1]);
        $sender->sendMessage(Format::PREFIX_CRATE . "aCrate made! Do /editcrate " . $args[0] . " to change the items.");
    }

}